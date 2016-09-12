<?php

declare(strict_types=1);

namespace Renamed;

use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

use Symfony\Component\Process\ProcessBuilder;

class RunTestsTest extends TestCase
{
    /** @test */
    function it_runs_the_test_suite_of_a_project_and_sees_that_it_passes()
    {
        // Given that we have an example project in ./examples/demo-phpunit
        $exampleDir = __DIR__ . '/../examples/demo-phpunit/';

        // and the phpunit executable can be found at vendor/bin/phpunit
        $command = [
            // 'vendor/bin/phpunit',
            'vendor/bin/phpspec run',
            '--bootstrap ../bootstrap_mutation.php'
        ];

        // when we run phpunit
        $process = new Process(implode(' ', $command), $exampleDir);
        $process->run();

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        echo $process->getOutput();
        // then we should see that the tests are passing
    }

    /** @test */
    function it_runs_the_test_suite_with_a_given_configuration_file()
    {
    }

    /** @test */
    function it_fails_when_the_given_configuration_file_is_invalid()
    {

    }

    /** @test */
    function it_runs_the_test_suite_with_a_given_bootstrap_file()
    {

    }


    /** @test */
    function it_runs_the_test_suite_of_a_project_with_a_given_mutation_and_sees_that_it_fails()
    {
        // Given that we have an example project in ./examples/demo-phpunit

        // and the phpunit executable can be found at vendor/bin/phpunit
        $command = [
            // 'vendor/bin/phpunit',
            'vendor/bin/phpspec run',
            '--bootstrap ../bootstrap_mutation.php'
        ];

        // and we have a mutation 'cell_mutation.php'
        $mutation = file_get_contents(__DIR__ . '/../examples/cell_mutation.php');

        // when we run phpunit
        $process = new Process(implode(' ', $command), __DIR__ . '/../examples/demo-phpunit/', null, $mutation);
        $process->run();

        // At least one test should have failed with the mutation
        $this->assertFalse($process->isSuccessful());
    }

    /** @test */
    function it_recusrively_lists_all_php_files_in_a_given_directory()
    {

        $path = realpath(__DIR__ . '/../examples/demo-phpunit/src/');

        $objects = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path),
            \RecursiveIteratorIterator::SELF_FIRST
        );
        $files = new \RegexIterator(
            $objects,
            '/^.+\.php$/i',
            \RecursiveRegexIterator::GET_MATCH
        );

        foreach($files as $name => $object){
            echo "$name\n";
        }
    }

}
