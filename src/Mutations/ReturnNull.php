<?php

declare(strict_types=1);

namespace Renamed\Mutations;

use PhpParser\Node;
use Renamed\MutationOperator;

final class ReturnNull implements MutationOperator
{
    /**
     * Replace `return STATEMENT;` with `STATEMENT; return;`
     * @param Node $node
     */
    public function mutate(Node $node)
    {
        // unless current body should not return null ...

        if (! $node instanceof Node\Stmt\Return_) {
            return;
        }

        if ($node->expr === null) {
            return;
        }

        yield [$node->expr, new Node\Stmt\Return_(null, $node->getAttributes())];
    }
}
