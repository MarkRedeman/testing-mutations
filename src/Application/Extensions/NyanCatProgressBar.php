<?php

declare(strict_types=1);

namespace Renamed\Application\Extensions;

use Renamed\Application\Context;
use Renamed\Application\Events\EventListener;
use Renamed\Application\Events;
use NyanCat;

final class NyanCatProgressBar implements Context
{
    public function __construct(Context $context)
    {
        $emitter = $context->eventEmitter();

        // Show Nyan Cat while waiting for our tests to run
        $emitter->subscribe(new class implements EventListener {

                private $scoreboard;

                public function __construct()
                {
                    // Show Nyan Cat while waiting for our tests to run
                    $this->scoreboard = new NyanCat\Scoreboard(
                        new NyanCat\Cat(),
                        new NyanCat\Rainbow(
                            \Fab\Factory::getFab(
                                empty($_SERVER['TERM']) ? 'unknown' : $_SERVER['TERM']
                            )
                        ),
                        [
                            new NyanCat\Team('killed', 'green', '^'),
                            new NyanCat\Team('escaped', 'red', 'o'),
                        ],
                        5
                    );
                }


                public function handle($event)
                {
                    if ($event === 'start') {
                        $this->scoreboard->start();
                    }
                    if ($event === Events\MutationKilled::class) {
                        $this->scoreboard->score('killed');
                    }
                    if ($event === Events\MutationEscaped::class) {
                        $this->scoreboard->score('escaped');
                    }
                    if ($event === 'stop') {
                        $this->scoreboard->stop();
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
