<?php

declare(strict_types=1);

namespace Renamed\Application\Extensions;

use Renamed\Application\Context;
use Renamed\Application\Events\EventListener;
use Renamed\Application\Events;

final class KeepTrackOfMutationsOnFile implements Context
{
    public function __construct(Context $context)
    {
        $emitter = $context->eventEmitter();

        // Show Nyan Cat while waiting for our tests to run
        $emitter->subscribe(new class implements EventListener {

                private $mutations = 0;

                public function handle($event)
                {
                    if ($event instanceof Events\MutationEscaped) {
                        $this->mutations++;

                        // Show the operator which has escaped
                        $operator = $event->mutation()->operator;
                        echo "Escaped: [${operator}]";
                        echo "[" . $event->mutation()->original()->getAttribute('startLine') .
                            ", " . $event->mutation()->original()->getAttribute('startLine') . "]";
                        echo "\n";

                    } elseif ($event instanceof Events\MutationsOnFileWereCompleted) {
                        echo "Tested " . $this->mutations . " mutations on [" . $event->filename() . " ] \n";

                        $this->mutations = 0;
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
