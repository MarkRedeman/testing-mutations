<?php

declare(strict_types=1);

namespace Renamed\tests\Mutations\Boolean;

use Renamed\Tests\MutationOperatorTest as TestCase;
use Renamed\Mutations\Boolean\LogicalAnd;
use Renamed\MutationOperator;

class LogicalAndTest extends TestCase
{
    protected function operator() : MutationOperator
    {
        return new LogicalAnd();
    }

    /** @test */
    public function it_mutates_and_to_or()
    {
        $this->mutates('if (true && true);')->to('if (true || true);');
    }

    /** @test */
    public function it_only_mutates_boolean_and_operators()
    {
        $this->doesNotMutate('$hello = "world";');
    }
}
