<?php

declare(strict_types=1);

namespace Renamed\Utility;

use DateTimeImmutable;

interface Clock
{
    public function time() : DateTimeImmutable;
}
