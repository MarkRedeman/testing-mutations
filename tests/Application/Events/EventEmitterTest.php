<?php

declare(strict_types=1);

namespace Renamed\Tests\Application\Events;

use Renamed\Application\Events\Event;
use Renamed\Application\Events\EventEmitter;
use Renamed\Application\Events\EventListener;
use PHPUnit_Framework_TestCase as TestCase;

class EventEmitterTest extends TestCase
{
    /** @test */
    function it_emits_an_event_to_all_of_its_subscribed_listeners()
    {
        $emitter = new EventEmitter;

        $listener = $this->prophesize(EventListener::class);
        $emitter->subscribe($listener->reveal());
        $event = $this->prophesize(Event::class);

        $emitter->emit($event->reveal());
        $listener->handle($event->reveal())->shouldHaveBeenCalled();
    }

}
