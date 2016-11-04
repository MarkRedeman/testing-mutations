<?php

declare(strict_types=1);

namespace Renamed\tests\Mutations\Arithmetic;

use Renamed\Tests\MutationOperatorTest as TestCase;
use Renamed\Mutations\Arithmetic\ShiftRight;
use Renamed\MutationOperator;

class ShiftRightTest extends TestCase
{
    protected function operator() : MutationOperator
    {
        return new ShiftRight();
    }

    /** @test */
    public function it_mutates_source_code()
    {
        $this->mutates('32 >> 5;')->to('32 << 5;');
    }

    /** @test */
    public function it_only_mutates_identical_signs()
    {
        $this->doesNotMutate('$hello = "world";');
    }
}
