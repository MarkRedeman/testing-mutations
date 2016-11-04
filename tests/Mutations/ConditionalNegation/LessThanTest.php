<?php

declare(strict_types=1);

namespace Renamed\tests\Mutations\ConditionalNegation;

use Renamed\Tests\MutationOperatorTest as TestCase;
use Renamed\Mutations\ConditionalNegation\LessThan;
use Renamed\MutationOperator;

class LessThanTest extends TestCase
{
    protected function operator() : MutationOperator
    {
        return new LessThan();
    }

    /** @test */
    public function it_mutates_less_than_to_not_less_than_or_equal()
    {
        $this->mutates('4 < 3;')->to('4 >= 3;');
    }

    /** @test */
    public function it_only_mutates_identical_signs()
    {
        $this->doesNotMutate('$hello = "world";');
    }
}
