<?php

declare(strict_types=1);

namespace Renamed\Application;

interface EventListener
{
    public function handle($event);
}
