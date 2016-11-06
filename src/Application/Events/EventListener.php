<?php

declare(strict_types=1);

namespace Renamed\Application\Events;

interface EventListener
{
    public function handle($event);
}
