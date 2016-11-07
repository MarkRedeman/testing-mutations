<?php

declare(strict_types=1);

namespace Renamed;

use PHPUnit_Framework_TestCase as TestCase;
use Renamed\Application\ApplicationContext;
use Renamed\Application\Context;
use Renamed\Application\Environment;
use Renamed\Application\Application;

class PHPUnitFeature extends TestCase
{
    /** @test */
    public function it_can_do_mutation_testing_using_phpunit()
    {
        $context = new ApplicationContext(
            new Environment(
                [],
                [
                    'project-path' =>  '/home/mark/Projects/renamed-humbug/pocs/test-with-mutation/examples/demo-phpunit/'
                ],
                [
                    'test-framework-executable' => 'vendor/bin/phpunit',
                    'test-framework-bootstrap' => 'bootstrap/autoload.php',
                    'test-framework-options' => ['--stop-on-failure',],
                    'target-directories' => ['src',],
                ]
            )
        );

        $app = new Application($context);
        $app->run();

        $this->assertEquals(0, $app->fails);
    }
}
