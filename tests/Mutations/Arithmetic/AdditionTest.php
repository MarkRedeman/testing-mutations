<?php

declare(strict_types=1);

namespace Renamed\tests\Mutations\Arithmetic;

use Renamed\Tests\MutationOperatorTest as TestCase;
use Renamed\Mutations\Arithmetic\Addition;
use Renamed\MutationOperator;

class AdditionTest extends TestCase
{
    protected function operator() : MutationOperator
    {
        return new Addition();
    }

    /** @test */
    public function it_mutates_source_code()
    {
        $this->mutates('4 + 3;')->to('4 - 3;');
    }

    /** @test */
    public function it_only_mutates_identical_signs()
    {
        $this->doesNotMutate('$hello = "world";');
    }
}
