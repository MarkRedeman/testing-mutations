<?php

declare(strict_types=1);

namespace WhiteWolf\Application;

final class Console
{
    private $context;

    // ConsoleContext
    public function __construct(ConsoleContext $context)
    {
        $this->context = $context;
        $this->subscribe($this->context->eventEmitter());
    }

    public function run()
    {
        $emitter = new Emmitter();
        $app = new WhiteWolf($env, $emitter);

        $app->run();
    }

    public function status() : int
    {
        return 0;
    }

    private function subscribe(EventEmitter $emitter)
    {
        $emitter->addListener('MutationGenerated', function ($event) {
            $this->mutationGenerated($event);
        });
    }
}
