<?php

declare(strict_types=1);

namespace Renamed\tests\Mutations\Boolean;

use Renamed\Tests\MutationOperatorTest as TestCase;
use Renamed\Mutations\Boolean\LogicalOr;
use Renamed\MutationOperator;

class LogicalOrTest extends TestCase
{
    protected function operator() : MutationOperator
    {
        return new LogicalOr();
    }

    /** @test */
    public function it_mutates_or_to_and()
    {
        $this->mutates('if (true || true);')->to('if (true && true);');
    }

    /** @test */
    public function it_only_mutates_boolean_and_operators()
    {
        $this->doesNotMutate('$hello = "world";');
    }
}
