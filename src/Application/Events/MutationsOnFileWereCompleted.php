<?php

declare(strict_types=1);

namespace Renamed\Application\Events;

use Renamed\Mutation;

final class MutationsOnFileWereCompleted
{
    private $filename;

    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }

    public function filename()
    {
        return $this->filename;
    }
}
