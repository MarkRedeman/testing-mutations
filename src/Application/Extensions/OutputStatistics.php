<?php

declare(strict_types=1);

namespace Renamed\Application\Extensions;

use Renamed\Application\Context;
use Renamed\Application\Events\EventListener;
use Renamed\Application\Events;
use Renamed\Utility\HighPrecisionClock;
use Renamed\Utility\Performance;

final class OutputStatistics implements Context
{
    public function __construct(Context $context)
    {
        $emitter = $context->eventEmitter();

        // Show Nyan Cat while waiting for our tests to run
        $emitter->subscribe(new class implements EventListener {

                private $runs = 0;
                private $fails = 0;

                 public function __construct()
                {
                    $this->performance = new Performance(
                        new HighPrecisionClock()
                    );
                }


                public function handle(Events\Event $event)
                {
                    if ($event instanceof Events\StartedApplication) {
                        $this->performance->start();
                    }

                    if ($event instanceof Events\MutationEscaped) {
                        $this->fails += 1;
                    }

                    if ($event instanceof Events\MutationTested) {
                        $this->runs += 1;
                    }
                    if ($event instanceof Events\FinishedApplication) {
                        echo "We've had: " . $this->runs . ' mutations of which ' . $this->fails . " escaped.\n";
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
