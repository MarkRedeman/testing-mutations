<?php

declare(strict_types=1);

namespace Renamed\Tests;

use PHPUnit_Framework_TestCase as TestCase;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\PrettyPrinter\Standard;
use PhpParser\Node;
use Renamed\ApplyMutation;
use Renamed\GenerateMutations;
use Renamed\Mutation;
use Renamed\MutationOperator;

class ApplyMutationTest extends TestCase
{
    use GenerateASTFromCode;

    /** @test */
    public function it_performs_an_action_after_a_mutation_has_been_applied()
    {
        $applied = false;
        $original = LNumber::fromString('1');
        $target = LNumber::fromString('2');

        $ast = [$original];
        $mutation = new Mutation($original, $target);

        $apply = new ApplyMutation($ast);
        $apply->apply($mutation, function (Mutation $mutation, $ast) use (&$applied, $target) {
            // Check that the AST has successfully been mutated
            $this->assertEquals([$target], $ast);
            $applied = true;
        });

        // Check that the action has been performed
        $this->assertTrue($applied);
    }

    /** @test */
    public function it_reverts_the_ast_when_finished()
    {
        // The AST will look like:
        // min(plus(4, mul(3, 2)), 1)
        $source = '<?php 4 + 3 * 2 - 1;';
        $ast = $this->generateASTFromCode($source);
        $apply = new ApplyMutation($ast);
        $applyMutation = function (Mutation $mutation) use ($apply, $source, $ast) {
            $apply->apply($mutation, function (Mutation $mutation, $ast) {
            });

            $this->assertEquals($this->generateASTFromCode($source), $ast);
        };

        $generate = new GenerateMutations(
            $applyMutation,
            new class() implements MutationOperator {
                public function mutate(Node $node)
                {
                    yield null;
                }
            }
        );

        $generate->generate($ast);

        $mutation = new Mutation($ast[0]->left->right->left, null);
        $apply = new ApplyMutation($ast);
        $apply->apply($mutation, function (Mutation $mutation, $ast) {
        });
        $this->assertEquals($this->generateASTFromCode($source), $ast);

        $mutation = new Mutation($ast[0]->left->right->right, null);
        $apply = new ApplyMutation($ast);
        $apply->apply($mutation, function (Mutation $mutation, $ast) {
        });
        $this->assertEquals($this->generateASTFromCode($source), $ast);

        $mutation = new Mutation($ast[0]->left->left, null);
        $apply = new ApplyMutation($ast);
        $apply->apply($mutation, function (Mutation $mutation, $ast) {
        });

        $this->assertEquals($this->generateASTFromCode($source), $ast);
    }

    /** @test */
    public function it_stops_stops_traversing_the_AST_when_it_has_applied_the_mutation()
    {
        $source = '<?php 2 / 3;';
        $ast = $this->generateASTFromCode($source);
        $two = $ast[0]->left;
        $ast[0]->right = $two;

        $mutation = new Mutation($two, LNumber::fromString('1'));
        $apply = new ApplyMutation($ast);
        $apply->apply($mutation, function (Mutation $mutation, $ast) {
            // var_dump( (new Standard)->prettyPrint($ast));
        });
        // var_dump($ast);
        // $this->assertEquals($this->generateASTFromCode($source), $ast);

        return;
        $mutate = new MutateSourceCode(
            new Multiplication()
        );

        $mutate->mutate($source, $this->storeAppliedMutations());

        $this->assertEquals([
            'echo 2 / 2 + 2 / 2;',
        ], $this->results);
    }
}
