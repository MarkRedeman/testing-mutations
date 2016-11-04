<?php

declare(strict_types=1);

namespace Renamed\tests\Mutations\ConditionalNegation;

use Renamed\Tests\MutationOperatorTest as TestCase;
use Renamed\Mutations\ConditionalNegation\Identical;
use Renamed\MutationOperator;

class IdenticalTest extends TestCase
{
    protected function operator() : MutationOperator
    {
        return new Identical();
    }

    /** @test */
    public function it_mutates_identical_to_not_identical()
    {
        $this->mutates('4 === 3;')->to('4 !== 3;');
    }

    /** @test */
    public function it_only_mutates_identical_signs()
    {
        $this->doesNotMutate('$hello = "world";');
    }
}
