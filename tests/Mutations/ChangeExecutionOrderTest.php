<?php

declare(strict_types=1);

namespace Renamed;

use Renamed\Tests\MutationOperatorTest as TestCase;
use Renamed\MutationOperator;

class ChangeExecutionOrderTest extends TestCase
{
    protected function operator() : MutationOperator
    {
        return new ChangeExecutionOrder;
    }

    /** @test */
    function it_changes_the_order_of_two_statements()
    {
        $source = <<<SOURCE
function hello() {
    echo "hello";
    echo "world";
};
SOURCE;

        $mutation = <<<MUTATION
function hello() {
    echo "world";
    echo "hello";
};
MUTATION;

        $this->mutates($source)->to($mutation);
    }

    /** @test */
    function it_ignores_single_statements()
    {

    }

}

use PhpParser\Node;

final class ChangeExecutionOrder implements MutationOperator
{

    /**
     * @param Node $node
     */
    public function mutate(Node $node)
    {
        if (! property_exists($node, 'stmts')) {
            return;
        }

        foreach ($this->permutations($node->stmts) as $permutation) {
            $permuted = clone $node;
            $permuted->stmts = $permutation;
            yield $permuted;
        }
    }

    private function permutations(array $elements)
    {
        if (count($elements) <= 1) {
            yield $elements;
        } else {
            foreach ($this->permutations(array_slice($elements, 1)) as $permutation) {
                foreach (range(0, count($elements) - 1) as $i) {
                    yield array_merge(
                        array_slice($permutation, 0, $i),
                        [$elements[0]],
                        array_slice($permutation, $i)
                    );
                }
            }
        }
    }
}
