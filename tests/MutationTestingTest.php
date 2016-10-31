<?php

declare(strict_types=1);

namespace Renamed;

use Closure;
use PHPUnit_Framework_TestCase as TestCase;
use PhpParser\PrettyPrinter\Standard;
use Renamed\MutateSourceCode;
use Renamed\Mutations;
use Renamed\Mutations\Multiplication;
use Renamed\Application\ApplicationContext;
use Renamed\Application\Context;
use Renamed\Application\Environment;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

class MutationTestingTest extends TestCase
{
    private $fails = 0;
    private $runs = 0;
    private $results = [];
    private $processes = [];
    private $done = 0;

    /** @test */
    function it_mutates_all_our_source_files()
    {
        $context = new ApplicationContext(
            new Environment(
                [],
                [
                    // formularium, phpspec
                    // 'project-path' =>  '/../examples/demo-phpunit/'
                    'project-path' => '/home/mark/Projects/renamed-humbug/pocs/test-with-mutation/examples/demo-phpunit/'
                    // 'project-path' =>  '/../examples/Core/'
                    // 'project-path' =>  '/../examples/formularium/'
                    // 'project-path' =>  '/../examples/phpspec/'
                ], [
                    // 'test-framework-executable' => 'vendor/bin/phpspec run',
                    // 'test-framework-executable' => 'bin/phpspec run',
                    'test-framework-executable' => 'vendor/bin/phpunit',
                    'test-framework-bootstrap' => 'bootstrap/autoload.php',
                    // 'test-framework-bootstrap' => 'vendor/autoload.php',
                    'test-framework-options' => [
                        '--stop-on-failure',
                        // '--format=dot',
                        // '--testsuite "Unit"'
                    ],
                    'target-directories' => [
                        'src'
                        // 'Accessor',
                        // 'Context',
                        // 'Currency',
                        // 'Dashboard',
                        // 'Distributor',
                        // 'Exception',
                        // 'Factory',
                        // 'Formatter',
                        // 'Metadata',
                        // 'Model',
                        // 'OrderProcessing',
                        // 'Payment',
                        // 'Pricing',
                        // 'Promotion',
                        // 'Provider',
                        // 'Remover',
                        // 'Repository',
                        // 'Resolver',
                        // 'Taxation',
                        // 'Test',
                        // 'Uploader'
                    ],
                    'test-frameworks' => [
                        'phpunit' => [
                            'executable' => 'vendor/bin/phpunit',
                            'bootstrap' => 'vendor/autoload.php',
                            'options' => [
                                '--stop-on-failure'
                            ]
                        ],
                        'phpspec' => [
                            'executable' => 'bin/phpspec',
                            'bootstrap' => 'vendor/autoload.php',
                            'options' => [
                                '--stop-on-failure'
                            ]
                        ],
                        'behat' => [
                            'executable' => 'vendor/bin/behat',
                            'bootstrap' => 'vendor/autoload.php',
                            'options' => [
                                '--stop-on-failure'
                            ]
                        ]
                    ]
                ]
            )
        );

        $application = new \Renamed\Application\Application($context);
        $application->run();
        return;
    }
}
