<?php

declare(strict_types=1);

namespace Renamed\Application;

use Renamed\MutateSourceCode;
use Renamed\MutationTester;
use Renamed\Mutation;
use Renamed\Mutations;
use Symfony\Component\Process\Process;
use Renamed\Application\Events;

/**
 *
 */
final class Application
{
    private $context;

    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    public function run()
    {
        // From context?
        $config = $this->context->config();
        $mutate = new MutateSourceCode(...$this->context->operators());

        $tester = new MutationTester(
            $projectPath = realpath($config['project-path']),
            $config['test-framework-executable'],
            realpath($config['project-path']. '/' . $config['test-framework-bootstrap']),
            $config['test-framework-options']
        );

        $files = $this->sourceFiles($config['project-path'], $config['target-directories']);
        $emitter = $this->context->eventEmitter();

        // Instead of having an emitter here, we could have
        // decorated MutationTesters and MutateSourceCode classes
        // that do the emitting
        $this->context->eventEmitter()->emit('start');

        // echo "Total files: " . count($files) . ".\n";
        foreach ($files as $name => $object) {
            $source = file_get_contents($name);

            $relName = substr($name, strlen($config['project-path']));
            echo "Mutating file: [${relName}]\n";

            $mutations = [];
            $mutate->mutate($source, function (Mutation $mutation, array $ast) use ($tester, $name, &$mutations) {
                // Filter mutation

                $this->emit(Events\MutationFound::class);
                // Mutation applied
                $result = $tester->testMutation($mutation, $ast, $name);

                if ($result->process->isSuccessful()) {
                    $this->emit(Events\MutationEscaped::class);

                    $m = $mutation->operator;
                    echo "Escaped: [${m}]";
                    echo "[" . $mutation->original()->getAttribute('startLine') .
                        ", " . $mutation->original()->getAttribute('startLine') . "]";
                    echo "\n";

                } else {
                    $this->emit(Events\MutationKilled::class);
                }
                // echo $result->process->getOutput() . "\n";

                // Mutation tested
                $this->emit(Events\MutationTested::class);
                $mutations[] = $mutation;
            });


            $emitter->emit(Events\MutationsOnFileWereCompleted::class);
            echo "Tested " . count($mutations) . " mutations on [${relName}] \n";
        }

        $this->emit('stop');

        return;
    }

    private function emit($event)
    {
        $this->context->eventEmitter()->emit($event);
    }

    private function sourceFiles($projectPath, $targetDirectories) : \Iterator
    {
        $append = new \AppendIterator();

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
