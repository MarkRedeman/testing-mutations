<?php

declare(strict_types=1);

namespace Renamed\Tests;

use PHPUnit_Framework_TestCase as TestCase;
use PhpParser\Lexer;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use Renamed\ApplyMutation;
use Renamed\GenerateMutations;
use Renamed\Mutation;
use Renamed\MutationOperator;
use Renamed\Mutations\Multiplication;

class CreateMutationsTest extends TestCase
{
    /** @test */
    function it_mutates_a_given_source_code()
    {
        return;
        // $source = file_get_contents(__DIR__ . '/stubs/Email.php');
        $source = "<?php 2 * 3 * 5;";

        $ast = $this->generateASTFromCode($source);
        var_dump($ast);
        return;
        $this->createMutations($source, function ($mutation, $ast) use (&$results) {
            echo (new Standard)->prettyPrint($ast) . "\n";
        }, new Multiplication);
    }

    private function createMutations(string $source, $action, MutationOperator ...$operators)
    {
        $ast = $this->generateASTFromCode($source);
        var_dump($ast);
        return;

        // Keep track of the mutated source code by adding the pretty printed
        // ASTs to the results array
        $apply = new ApplyMutation($ast);
        $afterGeneration = function (Mutation $mutation) use ($apply, $action) {
            $apply->apply($mutation, $action);
        };

        // Each node in the AST will be passed to the generator, which generates
        // a set of mutations for the given AST
        $generator = new GenerateMutations($afterGeneration, ...$operators);
        $generator->generate($ast);
    }

    private function generateASTFromCode(string $code) : array
    {
        $lexer = new Lexer(['usedAttributes' => ['startline', 'endline']]);
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7, $lexer);
        return $parser->parse($code);
    }
}
