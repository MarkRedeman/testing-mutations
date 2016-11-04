<?php

declare(strict_types=1);

namespace Renamed;

use Symfony\Component\Process\Process;

final class TestResult
{
    public $process;
    private $isSuccessful;
    private $output;


    public function __construct(Process $process, bool $isSuccessful, string $output)
    {
        $this->process = $process;
        $this->isSuccessful = $isSuccessful;
        $this->output = $output;
    }
}


// final class TestResult
// {
//     const POSITIVE = 'positive';
//     const NEGATIVE = 'negative';

//     public function __construct(Mutation $mutation)
//     {

//     }

//     public function failed() : bool
//     {

//     }

//     public function status()
//     {

//     }

// }
