<?php

declare(strict_types=1);

namespace Renamed;

use PhpParser\PrettyPrinter\Standard;
use Renamed\Application\Context;
use Symfony\Component\Process\Process;

final class MutationTester
{
    private $command;
    private $projectPath;
    private $bootstrapFile;

    // string $projectPath, string $executable, string $bootstrap = null, string $options = null
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
                'mutation-tests' => '',
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
        return '<?php ' . (new Standard())->prettyPrint(
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
                && $ast[0]->declares[0]->key == 'strict_types') {
                array_shift($ast);
            }
        }

        return $ast;
    }
}
