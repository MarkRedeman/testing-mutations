<?php

declare(strict_types=1);

namespace Renamed\tests\Mutations\ConditionalNegation;

use Renamed\Tests\MutationOperatorTest as TestCase;
use Renamed\Mutations\ConditionalNegation\NotEqual;
use Renamed\MutationOperator;

class NotEqualTest extends TestCase
{
    protected function operator() : MutationOperator
    {
        return new NotEqual();
    }

    /** @test */
    public function it_mutates_not_equal_to_equal()
    {
        $this->mutates('4 != 3;')->to('4 == 3;');
    }

    /** @test */
    public function it_only_mutates_not_equal_signs()
    {
        $this->doesNotMutate('$hello = "world";');
    }
}
