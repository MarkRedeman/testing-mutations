<?php

declare(strict_types=1);

namespace Renamed\Application;

// later we might want to make this an abstract or interface
final class ConsoleContext
{
    private $context;
    private $formatFactory;

    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    // We're returning a closure here so that don't have to define yet another
    // interface =)
    public function formatMutation() : Closure
    {
    }

    public function applicationContext() : Context
    {
    }
}
