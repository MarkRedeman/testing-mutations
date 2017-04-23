<?php

declare(strict_types=1);

namespace Renamed\Application\Events;

final class EventEmitter
{
    private $listeners = [];

    public function emit($event)
    {
        foreach ($this->listeners as $listener) {
            $listener->handle($event);
        }
    }

    public function subscribe(EventListener $listener)
    {
        $this->listeners[] = $listener;
    }
}
