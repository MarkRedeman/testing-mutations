<?php

declare(strict_types=1);

namespace Renamed\Mutations;

use Renamed\MutationOperator;
use PhpParser\Node;

final class AddReturnType implements MutationOperator
{
    public function __construct(GuessReturnType $guess)
    {
        $this->guess = $guess;
    }

    // this would be awesome
    public function mutate(Node $node)
    {
        if ($node != 'a function') {
            return;
        }

        if ($node->hasReturnType()) {
            return;
        }

        $this->addReturnTypeToFunction($node, $this->guess);
    }
}
