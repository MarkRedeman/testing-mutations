<?php

declare(strict_types=1);

namespace Renamed\tests\Mutations\ConditionalNegation;

use Renamed\Tests\MutationOperatorTest as TestCase;
use Renamed\Mutations\ConditionalNegation\Equal;
use Renamed\MutationOperator;

class EqualTest extends TestCase
{
    protected function operator() : MutationOperator
    {
        return new Equal();
    }

    /** @test */
    public function it_mutates_equal_to_not_equal()
    {
        $this->mutates('4 == 3;')->to('4 != 3;');
    }

    /** @test */
    public function it_only_mutates_equal_signs()
    {
        $this->doesNotMutate('$hello = "world";');
    }
}
