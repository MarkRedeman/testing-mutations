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

    /** @test */
    function it_mutates_all_our_source_files()
    {
        $context = new ApplicationContext(
            new Environment(
                [],
                [
                    // formularium, phpspec
                    'project-path' =>  '/../examples/demo-phpunit/'
                    // 'project-path' =>  '/../examples/Core/'
                    // 'project-path' =>  '/../examples/formularium/'
                    // 'project-path' =>  '/../examples/phpspec/'
                ], [
                    'test-framework-executable' => 'vendor/bin/phpspec run',
                    // 'test-framework-executable' => 'bin/phpspec run',
                    // 'test-framework-executable' => 'vendor/bin/phpunit',
                    // 'test-framework-bootstrap' => 'bootstrap/autoload.php',
                    'test-framework-bootstrap' => 'vendor/autoload.php',
                    'test-framework-options' => [
                        '--stop-on-failure',
                        // '--format=dot',
                        // '--testsuite "Unit"'
                    ],
                    'target-directories' => [
                        'src'
                        // 'Accessor',
                        // 'Context',
                        // 'Currency',
                        // 'Dashboard',
                        // 'Distributor',
                        // 'Exception',
                        // 'Factory',
                        // 'Formatter',
                        // 'Metadata',
                        // 'Model',
                        // 'OrderProcessing',
                        // 'Payment',
                        // 'Pricing',
                        // 'Promotion',
                        // 'Provider',
                        // 'Remover',
                        // 'Repository',
                        // 'Resolver',
                        // 'Taxation',
                        // 'Test',
                        // 'Uploader'
                    ],
                    'test-frameworks' => [
                        'phpunit' => [
                            'executable' => 'vendor/bin/phpunit',
                            'bootstrap' => 'vendor/autoload.php',
                            'options' => [
                                '--stop-on-failure'
                            ]
                        ],
                        'phpspec' => [
                            'executable' => 'bin/phpspec',
                            'bootstrap' => 'vendor/autoload.php',
                            'options' => [
                                '--stop-on-failure'
                            ]
                        ],
                        'behat' => [
                            'executable' => 'vendor/bin/behat',
                            'bootstrap' => 'vendor/autoload.php',
                            'options' => [
                                '--stop-on-failure'
                            ]
                        ]
                    ]
                ]
            )
        );

        $tester = new MutationTester($context);
        $files = $this->sourceFiles($context);

        foreach($files as $name => $object) {
            $source = file_get_contents($name);

            $mutate = new MutateSourceCode(...$context->operators());
            $mutate->mutate($source, function (Mutation $mutation, array $ast) use ($tester, $name) {
                $result = $tester->testMutation($mutation, $ast, $name);

                echo $this->runs;
                if ($result->process->isSuccessful()) {
                    $this->fails += 1;
                    echo " : Escaped\n";
                } else {
                    echo " : Killed\n";
                }
                echo $result->process->getOutput() . "\n";
                $this->runs += 1;
            });
        }

        echo "We've had: " . $this->runs . "mutations of which " . $this->fails . " escaped.\n";
    }

    private function sourceFiles(Context $context) : \Iterator
    {
        $config = $context->config();

        $append = new \AppendIterator;

        foreach ($config['target-directories'] as $path) {
            $append->append(
                new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator(
                        realpath(__DIR__ . $config['project-path']. $path)
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

        $projectPath = realpath(__DIR__ . $config['project-path']);

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
