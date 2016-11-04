<?php

declare(strict_types=1);

namespace Renamed\tests\Mutations\Boolean;

use Renamed\Tests\MutationOperatorTest as TestCase;
use Renamed\Mutations\Boolean\FalseValue;
use Renamed\MutationOperator;

class FalseValueTest extends TestCase
{
    protected function operator() : MutationOperator
    {
        return new FalseValue();
    }

    /** @test */
    public function it_mutates_true_to_false()
    {
        $this->mutates('true;')->to('false;');
    }

    /** @test */
    public function it_does_not_mutate_nodes_are_not_true()
    {
        $this->doesNotMutate('$hello = "world";');
        $this->doesNotMutate('false;');
    }
}
