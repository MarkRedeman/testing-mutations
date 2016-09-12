<?php

declare(strict_types=1);

namespace Renamed\Tests;

use Closure;
use PHPUnit_Framework_TestCase as TestCase;
use PhpParser\PrettyPrinter\Standard;
use Renamed\MutateSourceCode;
use Renamed\Mutations\Multiplication;
use Renamed\Mutations;

class MutationSourceCodeTest extends TestCase
{
    /**
     * Stores the applied mutations
     * @var array
     */
    private $results = [];

    /** @before */
    public function resetResulsts()
    {
        $this->results = [];
    }

    /**
     * This test shows how the process of generating, applying and storing
     * mutations works.
     * @test
     */
    function it_creates_mutations_based_on_an_abstract_syntax_tree()
    {
        $source = "<?php echo 1 * 2 * 3;";

        $mutate = new MutateSourceCode(
            new Multiplication
        );

        $mutate->mutate($source, $this->storeAppliedMutations());

        $this->assertEquals([
            'echo 1 / 2 * 3;',
            'echo 1 * 2 / 3;'
        ], $this->results);
    }

    /** @test */
    function it_should_not_swap_actual_code_with_mutations()
    {
        $source = "<?php echo 2 / 2 + 2 * 2;";

        $mutate = new MutateSourceCode(
            new Multiplication
        );

        $mutate->mutate($source, $this->storeAppliedMutations());

        $this->assertEquals([
            'echo 2 / 2 + 2 / 2;',
        ], $this->results);
    }

    /**
     * Saves the pretty printed mutated AST into the $results property
     */
    private function storeAppliedMutations() : Closure
    {
        return function ($mutation, $ast) {
            // var_dump($mutation, $ast);
            $this->results[] = (new Standard)->prettyPrint($ast);
        };
    }

    /**
     * When called with a mutation and AST, it will print
     * the prettyPrinted code
     */
    private function printAppliedMutations() : Closure
    {
        return function ($mutation, $ast) {
            echo (new Standard)->prettyPrint($ast);
        };
    }

    /** @test */
    function it_mutates_all_our_source_files()
    {
        $files = $this->sourceFiles();

        foreach($files as $name => $object) {
            $source = file_get_contents($name);

            $mutate = new MutateSourceCode(
                new Mutations\BinaryOperatorReplacement,
                new Mutations\DateTimeFromFormat,
                new Mutations\ReturnNull
            );

            $mutate->mutate($source, $this->storeAppliedMutations());
        }

        echo count($this->results);
    }

    private function sourceFiles() : \Iterator
    {
        $append = new \AppendIterator;

        foreach (['src', 'tests'] as $path) {
            $append->append(
                new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator(
                        realpath(__DIR__ . "/../${path}/")
                    ),
                    \RecursiveIteratorIterator::SELF_FIRST
                )
            );
        }

        $files = new \RegexIterator(
            $append,
            '/^.+\.php$/i',
            \RecursiveRegexIterator::GET_MATCH
        );

        return $files;
    }
}
