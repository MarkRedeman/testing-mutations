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


                public function handle($event)
                {
                    if ($event === 'start') {
                        $this->performance->start();
                    }

                    if ($event === Events\MutationEscaped::class) {
                        $this->fails += 1;
                    }

                    if ($event === Events\MutationTested::class) {
                        $this->runs += 1;
                    }

                    if ($event === Events\MutationsOnFileWereCompleted::class) {
                        // echo "Tested " . count($mutations) . " mutations on [${relName}] \n";
                    }
                    if ($event === 'stop') {
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
