<?php

declare(strict_types=1);

namespace Renamed\Application\Events;

use Renamed\Mutation;

final class MutationTested implements Event
{
    private $mutation;

    public function __construct(Mutation $mutation)
    {
        $this->mutation = $mutation;
    }

    public function mutation()
    {
        return $this->mutation;
    }
}
