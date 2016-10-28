<?php

declare(strict_types=1);

namespace Renamed;

use Closure;
use PHPUnit_Framework_TestCase as TestCase;
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

class MutationTestingTest extends TestCase
{
    private $fails = 0;
    private $runs = 0;
    private $results = [];
    private $processes = [];
    private $done = 0;

    // formularium, phpspec
    const PROJECT_PATH = '/../examples/formularium/';

    const PHPUNIT = [
        'vendor/bin/phpunit',
        '--bootstrap ../bootstrap_mutation.php',
        '--testsuite "Unit"',
        '--stop-on-failure'
    ];

    const PHPSPEC = [
        'vendor/bin/phpspec run',
        '--bootstrap ../bootstrap_mutation.php',
        '--stop-on-failure'
    ];

    /** @test */
    function it_mutates_all_our_source_files()
    {
        $context = new ApplicationContext(new Environment([], [
            'project-path' => self::PROJECT_PATH
        ], [
            'test-framework-executable' => 'vendor/bin/phpunit',
            'test-framework-bootstrap' => 'formularium/bootstrap/autoload.php',
            'test-framework-options' => ['--stop-on-failure', '--testsuite "Unit"']
        ]));

        $tester = new MutationTester($context);
        $files = $this->sourceFiles();

        foreach($files as $name => $object) {
            $source = file_get_contents($name);

            $mutate = new MutateSourceCode(...$context->operators());
            $mutate->mutate($source, function (Mutation $mutation, array $ast) use ($tester) {
                $result = $tester->testMutation($mutation, $ast);

                $current = $this->runs;
                $this->runs += 1;
                if ($result->process->isSuccessful()) {
                    $this->fails += 1;
                    echo "${current} : Escaped\n";
                    echo $result->process->getOutput() . "\n";
                } else {
                    echo "${current} : Killed\n";
                    echo $result->process->getOutput() . "\n";
                }
            });
        }

        echo "Total: " . count($this->results) . "\n";
        echo "Done: " . $this->done . "\n";
        echo "We've had: " . $this->runs . "mutations of which " . $this->fails . " escaped.\n";
    }

    private function sourceFiles() : \Iterator
    {
        $append = new \AppendIterator;

        foreach (['src'] as $path) {
            $append->append(
                new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator(
                        realpath(__DIR__ . self::PROJECT_PATH . $path)
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
    public $boot;

    public function __construct(Context $context)
    {
        $config = $context->config();

        $projectPath = realpath(__DIR__ . $config['project-path']);

        // Todo setpu bootstrap file
        // we probably want to do this in preperation phase
        $bootstrap = $projectPath . $config['test-framework-bootstrap'];

        $this->bootstrapFile = tmpfile();
        fwrite($this->bootstrapFile, $this->renderBootstrapFile($bootstrap));
        var_dump(stream_get_meta_data($this->bootstrapFile));
        $path = stream_get_meta_data($this->bootstrapFile)['uri'];
        $this->boot = $path;
        var_dump($path);


        // $path = $bootstrap;
        $command = implode(
            ' ',
            array_merge(
                [$config['test-framework-executable']],
                $config['test-framework-options'],
                ["--bootstrap formularium/bootstrap/autoload.php"]
            )
        );

        var_dump($command);
        $this->command = $command;
        $this->projectPath = $projectPath;

    }

    // Instead of having this function create the process we might as well have an object
    // that only returns the command, path, env and StdIn
    public function testMutation(Mutation $mutation, array $ast) : TestResult
    {
        // Pass the mutated code to STDIN, run and wait for result
        $process = new Process(
            $this->command,
            $this->projectPath,
            $env = [],
            $this->printAst($ast)
        );
        $process->start();

        var_dump($process->getOutput());

        // var_dump(file_get_contents($this->boot));
        return new TestResult($process, $process->isSuccessful(), $process->getOutput());
    }

    private function renderBootstrapFile($bootstrap) : string
    {
        return <<<EOT
#!/usr/bin/env php
<?php

require_once $bootstrap;


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
