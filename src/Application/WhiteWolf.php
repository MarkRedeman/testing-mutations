<?php

declare(strict_types=1);

namespace Renamed\Applicaiton;

/**
 *
 */
final class WhiteWolf
{
    private $context;

    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    public function run()
    {
        // From context?
        $mutate = new MutateSourceCode(...$context->operators());
        $tester = new MutationTester($context);
        $files = $this->sourceFiles($context);
        $emitter = $this->context->emitter();

        // Instead of having an emitter here, we could have
        // decorated MutationTesters and MutateSourceCode classes
        // that do the emitting

        foreach($files as $name => $object) {
            $source = file_get_contents($name);


            $mutate->mutate($source, function (Mutation $mutation, array $ast) use ($tester) {
                $emitter->emit(MutationFound::class);
                // Mutation applied
                $result = $tester->testMutation($mutation, $ast);

                // Mutation tested
                $emitter->emit(MutationTested::class);
            });

            $emitter->emit(MutationsOnFileWereCompleted::class)
        }

        return;
        $pipeline = new Pipeline;

        $locateSourceFiles = function(array $targetDirs, Closure $then) {
            $files = $this->sourceLocator->locate($targetDirs);

            // filter

            foreach ($files as $file) {
                $then($file);
            }
        }

        $parseToAst = function(string $fileContents, Closure $then) {
            $ast = $this->parser->parse($fileContents);

            return $then($ast);
        };

        // Would be nice to have
        $pipeline->pipe($locateSourceFiles)
            ->pipe($parseToAst)
            ->pipe($generateMutations) // should keep track of the operator and raise an event
            ->pipe($filterMutations) // should keep track of the filter class that filtered the mutation (and raise an event)
            ->pipe($applyMutation)
            ->pipe($testMutations)
            ->run();
    }

    private function sourceFiles(Context $context) : \Iterator
    {
        $config = $context->config();

        $append = new \AppendIterator;

        foreach (['src'] as $path) {
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

final class Pipeline
{
    private $pipes;

    public function pipe(Closure $fn) : self
    {
        $pipes[] = $fn;
        return $this;
    }

    public function run()
    {
        return $this->pipes[0]($this->pipes[1](/* ... */));
    }
}
