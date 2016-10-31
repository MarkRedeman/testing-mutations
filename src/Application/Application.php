<?php

declare(strict_types=1);

namespace Renamed\Application;

use Closure;
use PhpParser\PrettyPrinter\Standard;
use Renamed\MutateSourceCode;
use Renamed\Mutations;
use Renamed\Mutations\Multiplication;
use Renamed\Application\ApplicationContext;
use Renamed\Application\Context;
use Renamed\Application\Environment;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;
use Renamed\Mutation;

/**
 *
 */
final class Application
{
    private $context;
    private $runs = 0;

    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    public function run()
    {
        // From context?
        $mutate = new MutateSourceCode(...$this->context->operators());
        $tester = new MutationTester($this->context);

        $config = $this->context->config();
        $files = $this->sourceFiles($config['project-path'], $config['target-directories']);
        // $emitter = $this->context->emitter();

        // Instead of having an emitter here, we could have
        // decorated MutationTesters and MutateSourceCode classes
        // that do the emitting

        echo "Total files: " . count($files) . ".\n";
        foreach($files as $name => $object) {
            $source = file_get_contents($name);

            echo "Start mutating file: [${name}]\n";

            $mutate->mutate($source, function (Mutation $mutation, array $ast) use ($tester, $name) {


                // $emitter->emit(MutationFound::class);
                // Mutation applied
                $result = $tester->testMutation($mutation, $ast, $name);
                echo $this->runs;
                if ($result->process->isSuccessful()) {
                    $this->fails += 1;
                    echo " : Escaped\n";
                } else {
                    echo " : Killed\n";
                }
                // echo $result->process->getOutput() . "\n";
                $this->runs += 1;

                // Mutation tested
                // $emitter->emit(MutationTested::class);
            });

            // $emitter->emit(MutationsOnFileWereCompleted::class);
        }

        return;
    }

    private function sourceFiles($projectPath, $targetDirectories) : \Iterator
    {
        $append = new \AppendIterator;

        foreach ($targetDirectories as $path) {
            $append->append(
                new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator(
                        realpath($projectPath . $path)
                    ),
                    \RecursiveIteratorIterator::SELF_FIRST
                )
            );
        }

        $files = new \RegexIterator(
            $append,
            '/^.+\.php$/i',
            \RecursiveRegexIterator::GET_MATCH
        );

        return $files;
    }
}

final class TestResult
{
    public $process;
    private $isSuccessful;
    private $output;


    public function __construct(Process $process, bool $isSuccessful, string $output)
    {
        $this->process = $process;
        $this->isSuccessful = $isSuccessful;
        $this->output = $output;
    }
}

final class MutationTester
{
    private $command;
    private $projectPath;
    private $bootstrapFile;

    public function __construct(Context $context)
    {
        $config = $context->config();

        $projectPath = realpath($config['project-path']);

        // Todo setpu bootstrap file
        // we probably want to do this in preperation phase
        $bootstrap = $projectPath . '/' . $config['test-framework-bootstrap'];

        // Use a custom bootstrap file saved at a temporary location
        $this->bootstrapFile = tmpfile();
        fwrite($this->bootstrapFile, $this->renderBootstrapFile($bootstrap));
        $path = stream_get_meta_data($this->bootstrapFile)['uri'];

        $command = implode(
            ' ',
            array_merge(
                [$config['test-framework-executable']],
                ["--bootstrap ${path}"],
                $config['test-framework-options']
            )
        );

        $this->command = $command;
        $this->projectPath = $projectPath;
    }

    // Instead of having this function create the process we might as well have an object
    // that only returns the command, path, env and StdIn
    public function testMutation(Mutation $mutation, array $ast, string $name) : TestResult
    {
        // Pass the mutated code to STDIN, run and wait for result
        // Thought: adding environment variables containing the file that is being mutated
        // with its starting and ending line could make it possible for external extensions
        // (that is PHPUnit or PhpSpec extensions) to filter out certain tests
        $process = new Process(
            $this->command,
            $this->projectPath,
            $env = [
                'mutation-file' => $name,
                'mutation-star-lLine' => $mutation->original()->getAttributes()['startLine'],
                'mutation-end-line' => $mutation->original()->getAttributes()['endLine'],
                'mutation-tests' => ''
            ],
            $this->printAst($ast)
        );
        $process->run();

        return new TestResult($process, $process->isSuccessful(), $process->getOutput());
    }

    private function renderBootstrapFile(string $bootstrap) : string
    {
        return <<<EOT
<?php

require_once "$bootstrap";

eval('?>' . file_get_contents('php://stdin'));

EOT;
    }

    private function printAst(array $ast) : string
    {
        return '<?php ' . (new Standard)->prettyPrint(
            $this->trimDeclareStrict($ast)
        );
    }

    /**
     * Due to the way that we're executing mutated source code we will
     * have to remove delcare strict statements
     * Hopefully this is a temporary fix
     */
    private function trimDeclareStrict(array $ast) : array
    {
        if ($ast[0] instanceof \PhpParser\Node\Stmt\Declare_) {
            if ($ast[0]->declares[0] instanceof \PhpParser\Node\Stmt\DeclareDeclare
                && $ast[0]->declares[0]->key == "strict_types") {
                array_shift($ast);
            }
        }

        return $ast;
    }
}
