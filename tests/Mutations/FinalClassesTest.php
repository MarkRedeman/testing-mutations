<?php

declare(strict_types=1);

namespace Renamed\Tests\Mutations;

use Renamed\MutationOperator;
use Renamed\Mutations\ReturnNull;
use Renamed\Tests\MutationOperatorTest as TestCase;

// class FinalClassesTest extends TestCase
// {
//     protected function operator() : MutationOperator
//     {
//         return new FinalClasses;
//     }

//     /** @test */
//     function it_replaces_a_return_statement()
//     {
//         $this->mutates('class HelloWorld {}')->to('final class HelloWorld {}');
//     }

//     /** @test */
//     function it_only_mutates_return_statements()
//     {
//         $this->doesNotMutate('new StdClass;');
//         $this->doesNotMutate('$hello = "world";');
//     }
// }

// use PhpParser\Node;


// final class FinalClasses implements MutationOperator
// {
//     /**
//      * Replace `return STATEMENT;` with `STATEMENT; return;`
//      * @param Node $node
//      */
//     public function mutate(Node $node)
//     {
//         var_dump($node);
//         if (! $node instanceof Node\Stmt\Class_ && ! $node->isFinal()) {
//             return;
//         }

//         $new = clone $node;
//         $new->type = 32; // make it final TODO
//         yield $new;
//         // yield new Node\Stmt\Class_($node->name, $node->subnodes);
//     }
// }
