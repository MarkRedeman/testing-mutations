<?php

declare(strict_types=1);

namespace Renamed\TestingStrategies\TestResults;

// A test result could be MutationKilled, MutationEscaped or KilledByOtherProcess
// result might also include a list of tests that have killed the mutation
// maybe a result should include a list of tests that killed the mutant?
// TestResult -> { PHPUnitTestResult, PHPSpecTestResult }
final class TestResult
{

}
