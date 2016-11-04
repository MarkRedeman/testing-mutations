<?php

declare(strict_types=1);

namespace Renamed\tests\Mutations\Arithmetic;

use Renamed\Tests\MutationOperatorTest as TestCase;
use Renamed\Mutations\Arithmetic\ModEqual;
use Renamed\MutationOperator;

class ModEqualTest extends TestCase
{
    protected function operator() : MutationOperator
    {
        return new ModEqual();
    }

    /** @test */
    public function it_mutates_source_code()
    {
        $this->mutates('$var %= 3;')->to('$var *= 3;');
    }

    /** @test */
    public function it_only_mutates_identical_signs()
    {
        $this->doesNotMutate('$hello = "world";');
    }
}
