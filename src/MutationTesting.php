<?php

declare(strict_types=1);

namespace Renamed;

final class MutationTesting
{
    private $mutator;

    private $dispatcher;

    /**
     * @param string root - the base directory of the project which we want to test
     * @param Dispatcher $dispatcher - Event Dispatcher
     */
    public function __construct(MutateSourceCode $mutator, Dispatcher $dispatcher)
    {
    }

    /**
     * @param array $targets ['src', 'tests']
     */
    public function test(string $root, array $targets)
    {
        $files = $this->sourceFiles($root, $targets);

        foreach($files as $name => $object) {
            $source = file_get_contents($name);

            $this->mutator->mutate($source, $this->testMutation());
        }
    }

    /**
     * @param string $root '/projects/Francken/'
     * @param array $targets ['src', 'tests']
     */
    private function sourceFiles(string $root, array $targets) : \Iterator
    {
        $append = new \AppendIterator;

        // We want to find all files inside of the project's target directories
        foreach ($targets as $path) {
            $append->append(
                new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator(
                        realpath($root . '/' . $path)
                    ),
                    \RecursiveIteratorIterator::SELF_FIRST
                )
            );
        }

        // We only want to test php files
        $files = new \RegexIterator(
            $append,
            '/^.+\.php$/i',
            \RecursiveRegexIterator::GET_MATCH
        );

        return $files;
    }
}
