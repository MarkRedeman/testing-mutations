<?php

declare(strict_types=1);

namespace Renamed;

final class MutationTargets
{
    private $iterator;

    public function __construct(string $root, array $targets)
    {
        $append = new \AppendIterator;

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

        // Only target php files
        $files = new \RegexIterator(
            $append,
            '/^.+\.php$/i',
            \RecursiveRegexIterator::GET_MATCH
        );

        $this->iterator = $files;
    }

    public function iterate()
    {

    }
}
