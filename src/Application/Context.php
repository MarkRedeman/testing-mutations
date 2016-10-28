<?php

declare(strict_types=1);

namespace Renamed\Application;

interface Context
{
    public function eventEmitter() : EventEmitter;
    public function sourceLocator() : SourceLocator;
    public function operators() : array;
}

interface Extendable
{
    public function load(Extension $extension);
}

abstract class Extension implements Context
{
    abstract public function __construct(Environment $env);

    /**
     * The name of an extension is used in configuration files...
     */
    abstract public function name() : string;
}


// final class MemoizeContext implements Context
// {
//     //
// }

final class CodeCoverageContext
{

}

interface TestOrchestratorContext
{
    public function processBuilder() : Processbuilder;

    public function factory();
    // {
    //     return new Factory(
    //         new SomeContext
    //     );
    // }
}


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
