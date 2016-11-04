<?php

declare(strict_types=1);

namespace Renamed\tests\Mutations;

use Renamed\Tests\MutationOperatorTest as TestCase;
use Renamed\Mutations\Multiplication;
use Renamed\MutationOperator;

class MultiplicationTest extends TestCase
{
    protected function operator() : MutationOperator
    {
        return new Multiplication();
    }

    /** @test */
    public function it_replaces_a_multiplication_sign_with_a_division_sign()
    {
        $this->mutates('1 * 2;')->to('1 / 2;');
    }
}
