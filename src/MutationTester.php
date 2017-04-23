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

    public function __construct(
        string $projectPath,
        string $executable,
        string $bootstrap = null,
        array $options = []
    ) {
        // Use a custom bootstrap file saved at a temporary location
        $path = $this->setupMutationBoostrapFile($bootstrap);

        $command = implode(
            ' ',
            array_merge([$executable], ["--bootstrap ${path}"], $options)
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

    /**
     * Create a temporary bootstrap file which applies the mutation during
     * the tests.
     * This file is temporary and will be deleted once $this is removed
     * @return string path to bootstrap file
     */
    private function setupMutationBoostrapFile(string $bootstrap) : string
    {
        $this->bootstrapFile = tmpfile();
        fwrite($this->bootstrapFile, $this->renderBootstrapFile($bootstrap));

        return stream_get_meta_data($this->bootstrapFile)['uri'];
    }

    /**
     * First require the project's original bootstrap file and then simply
     * apply the mutation by evalling its contents
     *
     * Note that if strict typing is enabled this file should also have
     * enabled strict typing
     */
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
        return '<?php ' . (new Standard())->prettyPrint($ast);
    }
}
