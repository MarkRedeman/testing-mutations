<?php

declare(strict_types=1);

namespace Renamed;

use Renamed\MutationOperator;
use Renamed\Tests\MutationOperatorTest as TestCase;

class NumberToFloatTest extends TestCase
{
    protected function operator() : MutationOperator
    {
        return new IntToFloat;
    }

    /** @test */
    function it_uses_a_more_specific_type()
    {
        $source = '$f = function(int $int) {};';
        $mutation = '$f = function(float $int) {};';

        $node = new \PhpParser\Node\Param(
            'number',
            null,
            'int'
        );

        $this->assertEquals(
            new \PhpParser\Node\Param(
                'number',
                null,
                'float'
            ),
            $this->operator()->mutate($node)->current()
        );
    }

    /** @test */
    function it_only_mutates_integer_parameters()
    {
        $node = new \PhpParser\Node\Param(
            'number',
            null,
            'string'
        );

        $this->assertNotEquals(
            new \PhpParser\Node\Param(
                'number',
                null,
                'float'
            ),
            $this->operator()->mutate($node)->current()
        );

    }


    /** @test */
    function it_only_mutates_parameters()
    {
        $this->doesNotMutate("echo 'hello';");
    }
}

use PhpParser\Node;

/**
 * Changes a int typehint to float
 */
final class IntToFloat implements MutationOperator
{
    /**
     * Replace `return STATEMENT;` with `STATEMENT; return;`
     * @param Node $node
     */
    public function mutate(Node $node)
    {
        if (! $node instanceof Node\Param) {
            return;
        }

        if ($node->type !== 'int') {
            return;
        }

        yield new Node\Param(
            $node->name,
            $node->default,
            'float',
            $node->byRef,
            $node->variadic,
            $node->getAttributes()
        );
    }
}
