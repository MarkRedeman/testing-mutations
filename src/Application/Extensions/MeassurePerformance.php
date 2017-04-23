<?php

declare(strict_types=1);

namespace Renamed\Application\Extensions;

use Renamed\Application\Context;
use Renamed\Application\Events\EventListener;
use Renamed\Application\Events;
use Renamed\Utility\HighPrecisionClock;
use Renamed\Utility\Performance;

final class MeassurePerformance implements Context
{
    public function __construct(Context $context)
    {
        $emitter = $context->eventEmitter();

        // Show Nyan Cat while waiting for our tests to run
        $emitter->subscribe(new class implements EventListener {

                private $performance;

                 public function __construct()
                {
                    $this->performance = new Performance(
                        new HighPrecisionClock()
                    );
                }


                public function handle($event)
                {
                    if ($event instanceof Events\StartedApplication) {
                        $this->performance->start();
                    }
                    if ($event instanceof Events\FinishedApplication) {
                        $this->performance->stop();
                        echo 'Time: ' . $this->performance->formatTime() . "\n";
                        echo 'Memory: ' . $this->performance->formatMemoryUsage() . "\n";
                    }
                }
            }
        );
    }

    public function operators() : array
    {
        return [];
    }
}
