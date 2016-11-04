<?php

declare(strict_types=1);

namespace Renamed\tests\Mutations\ConditionalNegation;

use Renamed\Tests\MutationOperatorTest as TestCase;
use Renamed\Mutations\ConditionalNegation\NotIdentical;
use Renamed\MutationOperator;

class NotIdenticalTest extends TestCase
{
    protected function operator() : MutationOperator
    {
        return new NotIdentical();
    }

    /** @test */
    public function it_mutates_not_identical_to__identical()
    {
        $this->mutates('4 !== 3;')->to('4 === 3;');
    }

    /** @test */
    public function it_only_mutates_not_identical_signs()
    {
        $this->doesNotMutate('$hello = "world";');
    }
}
