<?php

declare(strict_types=1);

namespace Renamed;

use Closure;
use PHPUnit_Framework_TestCase as TestCase;
use PhpParser\PrettyPrinter\Standard;
use Renamed\MutateSourceCode;
use Renamed\Mutations;
use Renamed\Mutations\Multiplication;
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
        $files = $this->sourceFiles();

        foreach($files as $name => $object) {
            $source = file_get_contents($name);

            $mutate = new MutateSourceCode(
                // new Mutations\BinaryOperatorReplacement,
                new Mutations\DateTimeFromFormat,
                new Mutations\ReturnNull,
                new Mutations\Arithmetic\Addition,
                new Mutations\Arithmetic\BitwiseAnd,
                new Mutations\Arithmetic\BitwiseOr,
                new Mutations\Arithmetic\BitwiseXor,
                new Mutations\Arithmetic\DivEqual,
                new Mutations\Arithmetic\Division,
                new Mutations\Arithmetic\Exponentiation,
                new Mutations\Arithmetic\MinusEqual,
                new Mutations\Arithmetic\ModEqual,
                new Mutations\Arithmetic\Modulus,
                new Mutations\Arithmetic\MulEqual,
                new Mutations\Arithmetic\Multiplication,
                new Mutations\Arithmetic\Not,
                new Mutations\Arithmetic\PlusEqual,
                new Mutations\Arithmetic\PowEqual,
                new Mutations\Arithmetic\ShiftLeft,
                new Mutations\Arithmetic\ShiftRight,
                new Mutations\Arithmetic\Subtraction,
                new Mutations\Boolean\FalseValue,
                new Mutations\Boolean\LogicalAnd,
                new Mutations\Boolean\LogicalLowerAnd,
                new Mutations\Boolean\LogicalLowerOr,
                new Mutations\Boolean\LogicalNot,
                new Mutations\Boolean\LogicalOr,
                new Mutations\Boolean\TrueValue,
                new Mutations\ConditionalBoundary\GreaterThan,
                new Mutations\ConditionalBoundary\GreaterThanOrEqual,
                new Mutations\ConditionalBoundary\LessThan,
                new Mutations\ConditionalBoundary\LessThanOrEqual,
                new Mutations\ConditionalNegation\Equal,
                new Mutations\ConditionalNegation\GreaterThan,
                new Mutations\ConditionalNegation\GreaterThanOrEqual,
                new Mutations\ConditionalNegation\Identical,
                new Mutations\ConditionalNegation\LessThan,
                new Mutations\ConditionalNegation\LessThanOrEqual,
                new Mutations\ConditionalNegation\NotEqual,
                new Mutations\ConditionalNegation\NotIdentical,
                new Mutations\Increment\Decrement,
                new Mutations\Increment\Increment,
                new Mutations\Number\FloatValue
                // new Mutations\Number\IntegerValue
            );

            $mutate->mutate($source, $this->testMutation());
            // $mutate->mutate($source, $this->storeAppliedMutations());

        }

        // $waits = 0;
        // while ($this->done != $this->runs && $waits < 10) {
        //     sleep(1);
        //     echo "Waiting";
        //     $waits += 1;
        // }

        // $total = count($this->processes);
        // $waits = 0;
        // while ($this->done < $this->runs && $waits < 20) {
        //     $current = 0;


        //     for ($idx = 0; $idx < $total ;$idx++ ) {
        //         if (! isset($this->processes[$idx]) || $this->processes[$idx] == null) {
        //             continue;
        //         }

        //         $process = $this->processes[$idx];

        //         if (! $process->isRunning()) {
        //             if ($process->isSuccessful()) {
        //                 $this->fails += 1;
        //                 echo "${idx} : Killed\n";
        //             } else {
        //                 echo "${idx} : Escaped\n";
        //                 echo $process->getOutput() . "\n";
        //             }
        //             $this->done += 1;
        //             $this->processes[$idx] = null;
        //         }
        //     }


        //     if (! $this->done == $this->runs) {
        //         sleep(1);
        //         $waits += 1;
        //     }
        // }

        // var_dump(count($this->processes), $waits);

        echo "\nWaited: ${waits}\n";
        // var_dump($this->processes);
        echo "Total: " . count($this->results) . "\n";
        // var_dump($this->done, $this->runs);
        echo "Done: " . $this->done . "\n";
        echo "We've had: " . $this->runs . "mutations of which " . $this->fails . " escaped.\n";
    }

    /**
     * Saves the pretty printed mutated AST into the $results property
     */
    private function testMutation() : Closure
    {
        return function ($m, $ast) {
            // Print the mutation as executable code
            $mutation = '<?php ' . (new Standard)->prettyPrint(
                $this->trimDeclareStrict($ast)
            );

            // Note: at this point we could also start a process for PHPSpec
            // Behat and other testing tools.
            // Whenever one of these tools kills a mutation we can then stop the
            // other processes
            // This means that we will have to wrap the testing of a mutation
            // inside of an object that keeps track of the different processes that
            // are being run.
            // When doing stuff in parallel we will want to limit the amount of
            // processes that are being run.
            // We could make it so that only one mutation is being tested at a time,
            // meaning multiple testing frameworks could be used for one mutation.
            // Or we will have to keep track of the running processes in a different
            // object (which might also be responsible for starting processes?)

            // TestRunner PHPUnitRunner
            // TestRunnerResult { mutation, killed_by, subject }

            // A test result could be MutationKilled, MutationEscaped or KilledByOtherProcess
            // result might also include a list of tests that have killed the mutation
            // maybe a result should include a list of tests that killed the mutant?
            // TestResult -> { PHPUnitTestResult, PHPSpecTestResult }


            // At this point I should get a list of all tests run on the mutated code
            // if there are no tests than it makes no sense to test this mutation,
            // so we will can skip this one and mark it as uncovered
            // Note that this might depend on the Test Framework that we're using

            // and the phpunit executable can be found at vendor/bin/phpunit
            $command = [
                // 'vendor/bin/phpunit',
                // 'vendor/bin/phpspec run',
                'bin/phpspec run',
                '--bootstrap ../bootstrap_mutation.php',
                // '--testsuite "Unit"',
                '--stop-on-failure'
            ];

            $current = $this->runs;

            // when we run phpunit
            // $process = new Process(implode(' ', $command), __DIR__ . '/../examples/formularium/', null, $mutation);
            $process = new Process(implode(' ', $command), __DIR__ . '/../examples/phpspec/', null, $mutation);
            // $process = new Process(implode(' ', $command), __DIR__ . '/../examples/demo-phpunit/', null, $mutation);
            // $process->start(); // Time: 9.35 seconds, Memory: 10.00MB
            $process->run(); // 14.33, 10MB
            // $this->processes[count($this->processes)] = $process;
            // $process->start(function() {

            // });
            // $process->run(function ($type, $buffer) use ($current, $process) {
            //     // var_dump($process->isSuccessful(), $process->isRunning());

            //     if (! $process->isRunning()) {
            //         if (Process::ERR === $type) {
            //             echo "${current} : Escaped\n";
            //             $this->fails += 1;
            //             // echo 'ERR > '.$buffer; // this will not be called
            //         } else {
            //             // echo 'OUT > '.$buffer; // but this is ok
            //             echo "${current} : Killed\n";
            //         }
            //         $this->done += 1;
            //         echo "Done: " . $this->done . "\n";

            //     }
            // });

            while ($process->isRunning()) {
                // waiting for process to finish
            }

            if ($process->isSuccessful()) {
                $this->fails += 1;
                // var_dump($m);
                echo "${current} : Escaped\n";
                // echo $process->getOutput() . "\n";
                // echo $mutation . "\n";
            } else {
                echo "${current} : Killed\n";
                // echo $process->getOutput() . "\n";
                // echo $mutation . "\n";
            }

            $this->runs += 1;
            // At least one test should have failed with the mutation
            // $this->assertFalse($process->isSuccessful());
        };
    }


    private function sourceFiles() : \Iterator
    {
        $append = new \AppendIterator;

        // foreach (['src/Domain/Indications'] as $path) {
        foreach (['src'] as $path) {
            $append->append(
                new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator(
                        // realpath(__DIR__ . "/../examples/formularium/${path}/")
                        realpath(__DIR__ . "/../examples/phpspec/${path}/")
                    ),
                    \RecursiveIteratorIterator::SELF_FIRST
                )
            );
        }

        $files = new \RegexIterator(
            $append,
            '/^.+\.php$/i',
            \RecursiveRegexIterator::GET_MATCH
        );

        return $files;
    }

    /**
     * Saves the pretty printed mutated AST into the $results property
     */
    private function storeAppliedMutations() : Closure
    {
        return function ($mutation, $ast) {
            $this->results[] = (new Standard)->prettyPrint($ast);
        };
    }

    /**
     * When called with a mutation and AST, it will print
     * the prettyPrinted code
     */
    private function printAppliedMutations() : Closure
    {
        return function ($mutation, $ast) {
            echo (new Standard)->prettyPrint($ast);
        };
    }

    /**
     * Due to the way that we're executing mutated source code we will
     * have to remove delcare strict statements
     * Hopefully this is a temporary fix
     */
    private function trimDeclareStrict(array $ast) : array
    {
        if ($ast[0] instanceof \PhpParser\Node\Stmt\Declare_) {
            if ($ast[0]->declares[0] instanceof \PhpParser\Node\Stmt\DeclareDeclare
            && $ast[0]->declares[0]->key == "strict_types") {
                array_shift($ast);
            }
        }


        return $ast;
    }
}

/**
 * This ProcessManager is a simple wrapper to enable parallel processing using Symfony Process component.
 */
class ProcessManager
{
    /**
     * @param Process[] $processes
     * @param int $maxParallel
     * @param int $poll
     */
    public function runParallel(array $processes, $maxParallel, $poll = 1000)
    {
        $this->validateProcesses($processes);

        // do not modify the object pointers in the argument, copy to local working variable
        $processesQueue = $processes;

        // fix maxParallel to be max the number of processes or positive
        $maxParallel = min(abs($maxParallel), count($processesQueue));

        // get the first stack of processes to start at the same time
        /** @var Process[] $currentProcesses */
        $currentProcesses = array_splice($processesQueue, 0, $maxParallel);

        // start the initial stack of processes
        foreach ($currentProcesses as $process) {
            $process->start();
        }

        do {
            // wait for the given time
            usleep($poll);

            // remove all finished processes from the stack
            foreach ($currentProcesses as $index => $process) {
                if (!$process->isRunning()) {
                    unset($currentProcesses[$index]);

                    // directly add and start new process after the previous finished
                    if (count($processesQueue) > 0) {
                        $nextProcess = array_shift($processesQueue);
                        $nextProcess->start();
                        $currentProcesses[] = $nextProcess;
                    }
                }
            }
            // continue loop while there are processes being executed or waiting for execution
        } while (count($processesQueue) > 0 || count($currentProcesses) > 0);
    }

    /**
     * @param Process[] $processes
     */
    protected function validateProcesses(array $processes)
    {
        if (empty($processes)) {
            throw new \InvalidArgumentException('Can not run in parallel 0 commands');
        }

        foreach ($processes as $process) {
            if (!($process instanceof Process)) {
                throw new \InvalidArgumentException('Process in array need to be instance of Symfony Process');
            }
        }
    }
}
