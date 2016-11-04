<?php

declare(strict_types=1);

namespace Renamed\tests\Mutations\Boolean;

use Renamed\Tests\MutationOperatorTest as TestCase;
use Renamed\Mutations\Boolean\LogicalNot;
use Renamed\MutationOperator;

class LogicalNotTest extends TestCase
{
    protected function operator() : MutationOperator
    {
        return new LogicalNot();
    }

    /** @test */
    public function it_mutates_true_to_false()
    {
        $this->mutates('if (! true);')->to('if (true);');
    }

    /** @test */
    public function it_only_mutates_boolean_and_operators()
    {
        $this->doesNotMutate('$hello = "world";');
    }
}
