<?php

declare(strict_types=1);

namespace Renamed\Tests\Mutations;

use Renamed\MutationOperator;
use Renamed\Tests\MutationOperatorTest as TestCase;

class VisibilityAccessTest extends TestCase
{
    protected function operator() : MutationOperator
    {
        return new VisibilityAccess();
    }

    /** @test */
    public function it_makes_classes_final()
    {
        $this->mutates('class HelloWorld {}')->to('final class HelloWorld {}');
        // $this->mutates('class HelloWorld {public $hello;}')->to('class HelloWorld {private $hello;}');
    }

    /** @test */
    public function it_only_mutates_public_classes()
    {
        $this->doesNotMutate('final class HelloWorld {}');
        $this->doesNotMutate('abstract class HelloWorld {}');
        $this->doesNotMutate('interface HelloWorld {}');
    }

    /** @test */
    public function it_only_mutates_return_statements()
    {
        $this->doesNotMutate('new StdClass;');
        $this->doesNotMutate('$hello = "world";');
    }
}

use PhpParser\Node;

/**
 * Changes the visibility of properties (and methods?) changing
 * public -> protected | private
 * protected -> private
 */
final class VisibilityAccess implements MutationOperator
{
    /**
     * Replace `return STATEMENT;` with `STATEMENT; return;`
     * @param Node $node
     */
    public function mutate(Node $node)
    {
        if (! $node instanceof Node\Stmt\Class_) {
            return;
        }

        if ($node->flags == Node\Stmt\Class_::MODIFIER_FINAL
            || $node->flags == Node\Stmt\Class_::MODIFIER_ABSTRACT) {
            return;
        }

        yield new Node\Stmt\Class_(
            $node->name,
            [
                'flags' => Node\Stmt\Class_::MODIFIER_FINAL,
                'extends' => $node->extends,
                'implements' => $node->implements,
                'stmts' => $node->stmts,

            ],
            $node->getAttributes()
        );
    }
}
