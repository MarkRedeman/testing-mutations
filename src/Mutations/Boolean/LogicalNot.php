<?php

declare(strict_types=1);

namespace Renamed\Mutations\Boolean;

use PhpParser\Node;
use Renamed\MutationOperator;

/**
 * Removes !
 */
final class LogicalNot implements MutationOperator
{
    /**
     * @param Node $node
     */
    public function mutate(Node $node)
    {
        if (! $node instanceof Node\Expr\BooleanNot) {
            return;
        }

        yield $node->expr;
    }
}
