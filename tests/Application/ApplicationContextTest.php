<?php

declare(strict_types=1);

namespace Renamed\Tests\Application;

use PHPUnit_Framework_TestCase as TestCase;
use Renamed\Application\ApplicationContext;
use Renamed\Application\Environment;

/**
 * The ApplicationContext can be seen as a service container specifically
 * designed for our application.
 * Most of its methods are simple getters through the given environment object
 * later it should be possible to extend a context so that other extensions
 * may add behavior
 */
class ApplicationContextTest extends TestCase
{

    /**
     * @dataProvider configurationProvider
     * @test
     */
    function it_has_access_to_the_applications_configuration(array $config)
    {

        $env = new Environment([], [], $config);
        $context = new ApplicationContext($env);

        $this->assertEquals($config, $context->config());
    }

    /** @test */
    function it_has_a_set_of_default_mutation_operators()
    {
        $env = new Environment([], [], []);
        $context = new ApplicationContext($env);

        $operators = $context->operators();

        $this->assertCount(42, $operators);
    }

    /** @test */
    function it_loads_the_operators_of_its_extensions()
    {
        $env = new Environment([], [], [
            'extensions' => [FakeExtension::class]
        ]);
        $context = new ApplicationContext($env);

        $operators = $context->operators();

        $this->assertCount(43, $operators);
    }


    /** @test */
    function it_contains_an_event_emitter()
    {
        $env = new Environment([], [], []);
        $context = new ApplicationContext($env);

        $emitter = $context->eventEmitter();

        $this->assertTrue(
            $emitter === $context->eventEmitter(),
            'The context should always return the same emitter'
        );
    }


    public function configurationProvider()
    {
        return [
            [[]],
            [['test-framework' => 'phupnit']]
        ];
    }
}

use Renamed\Application\Context;
use Renamed\Application\Events\EventEmitter;
class FakeExtension implements Context
{
    public function __construct()
    {
    }

    public function eventEmitter() : EventEmitter
    {
    }

    // public function sourceLocator() : SourceLocator;
    public function operators() : array
    {
        return ['moi'];
    }
}
