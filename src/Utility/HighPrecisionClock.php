<?php

declare(strict_types=1);

namespace Renamed\Utility;

use DateTimeImmutable;

final class HighPrecisionClock implements Clock
{
    private $time;

    public function time() : DateTimeImmutable
    {
        return DateTimeImmutable::createFromFormat(
            'U.u',
            (string)microtime(true)
        );
    }
}
