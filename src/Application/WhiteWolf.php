<?php

declare(strict_types=1);

namespace Renamed\Applicaiton;

/**
 *
 */
final class WhiteWolf
{
    public function run()
    {
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
