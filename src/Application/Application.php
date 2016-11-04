<?php

declare(strict_types=1);

namespace Renamed\Application;

use Closure;
use PhpParser\PrettyPrinter\Standard;
use Renamed\Application\ApplicationContext;
use Renamed\Application\Context;
use Renamed\Application\Environment;
use Renamed\MutateSourceCode;
use Renamed\MutationTester;
use Renamed\TestResult;
use Renamed\Mutation;
use Renamed\Mutations;
use Renamed\Mutations\Multiplication;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

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
        $this->scoreboard = new \NyanCat\Scoreboard(
            new \NyanCat\Cat(),
            new \NyanCat\Rainbow(
                \Fab\Factory::getFab(
                    empty($_SERVER['TERM']) ? 'unknown' : $_SERVER['TERM']
                )
            ),
            [
                new \NyanCat\Team('killed', 'green', '^'),
                new \NyanCat\Team('escaped', 'red', 'o'),
            ],
            5
            // callable
        );
    }

    public function run()
    {
        \Renamed\Performance::start();
        // From context?
        $mutate = new MutateSourceCode(...$this->context->operators());
        $tester = new MutationTester($this->context);

        $config = $this->context->config();
        $files = $this->sourceFiles($config['project-path'], $config['target-directories']);
        // $emitter = $this->context->emitter();

        // Instead of having an emitter here, we could have
        // decorated MutationTesters and MutateSourceCode classes
        // that do the emitting
        $this->scoreboard->start();

        // echo "Total files: " . count($files) . ".\n";
        foreach($files as $name => $object) {
            $source = file_get_contents($name);

            $relName = substr($name, strlen($config['project-path']));
            echo "Mutating file: [${relName}]\n";

            $mutate->mutate($source, function (Mutation $mutation, array $ast) use ($tester, $name) {

                // Filter mutation


                // $emitter->emit(MutationFound::class);
                // Mutation applied
                $result = $tester->testMutation($mutation, $ast, $name);

                if ($result->process->isSuccessful()) {
                    $this->fails += 1;
                    $this->scoreboard->score('escaped');
                } else {
                    $this->scoreboard->score('killed');
                }
                // echo $result->process->getOutput() . "\n";
                $this->runs += 1;

                // Mutation tested
                // $emitter->emit(MutationTested::class);
            });

            // $emitter->emit(MutationsOnFileWereCompleted::class);
        }

        $this->scoreboard->stop();
        echo "We've had: " . $this->runs . " mutations of which " . $this->fails . " escaped.\n";

        \Renamed\Performance::stop();
        echo "Time: " . \Renamed\Performance::getTimeString() . "\n"; // 36640
        echo "Memory: " . \Renamed\Performance::getMemoryUsageString() . "\n"; // 36640

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
