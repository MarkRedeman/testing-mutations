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
        $this->context->eventEmitter()->emit(new Events\StartedApplication());

        // echo "Total files: " . count($files) . ".\n";
        foreach ($files as $name => $object) {
            $source = file_get_contents($name);

            $mutations = [];
            $mutate->mutate($source, function (Mutation $mutation, array $ast) use ($tester, $name, &$mutations) {
                // Filter mutation

                $this->emit(new Events\MutationFound($mutation));
                // Mutation applied
                $result = $tester->testMutation($mutation, $ast, $name);

                if ($result->process->isSuccessful()) {
                    // $this->emit(Events\MutationEscaped::class);
                    $this->emit(new Events\MutationEscaped($mutation));
                } else {
                    $this->emit(new Events\MutationKilled($mutation));
                }
                // echo $result->process->getOutput() . "\n";

                // Mutation tested
                $this->emit(new Events\MutationTested($mutation));
                $mutations[] = $mutation;
            });

            $emitter->emit(new Events\MutationsOnFileWereCompleted(
                substr($name, strlen($config['project-path']))
            ));
        }

        $this->emit(new Events\FinishedApplication());

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
