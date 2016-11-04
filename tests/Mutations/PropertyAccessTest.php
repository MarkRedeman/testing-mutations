<?php

// declare(strict_types=1);

// namespace Renamed\Tests\Mutations;

// use Renamed\MutationOperator;
// use Renamed\Mutations\ReturnNull;
// use Renamed\Tests\MutationOperatorTest as TestCase;

// class PropertyAccessTest extends TestCase
// {
//     protected function operator() : MutationOperator
//     {
//         return new PropertyAccess;
//     }

//     /** @test */
//     function it_replaces_a_return_statement()
//     {
//         $this->mutates('class HelloWorld {}')->to('final class HelloWorld {}');
//         // $this->mutates('class HelloWorld {public $hello;}')->to('class HelloWorld {private $hello;}');
//     }

//     /** @test */
//     function it_only_mutates_return_statements()
//     {
//         $this->doesNotMutate('new StdClass;');
//         $this->doesNotMutate('$hello = "world";');
//     }
// }

// use PhpParser\Node;

// final class PropertyAccess implements MutationOperator
// {
//     /**
//      * Replace `return STATEMENT;` with `STATEMENT; return;`
//      * @param Node $node
//      */
//     public function mutate(Node $node)
//     {
//         var_dump($node);
//         if (! $node instanceof Node\Stmt\Property) {
//             return;
//         }

//         yield [$node->expr, new Node\Stmt\Return_(null, $node->getAttributes())];
//     }
// }
