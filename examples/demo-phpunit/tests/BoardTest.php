<?php

namespace Test\Kata\Conway;

use PHPUnit_Framework_TestCase;
use Kata\Conway\Cell;
use Kata\Conway\Board;

class BoardTest extends PHPUnit_Framework_TestCase
{
    /** @test */
    public function it_should_spawn_an_empty_board_if_empty()
    {
        $liveCells = (new Board())->spawn()->getLiveCells();
        $this->assertCount(0, $liveCells);
    }

    /** @test */
    public function it_should_be_empty_if_seeded_with_an_empty_list()
    {
        $board = new Board();
        $board->seed([]);
        $this->assertCount(0, $board->getLiveCells());
    }

    /** @test */
    public function it_is_filled_when_seeded_with_a_list()
    {
        $board = new Board();
        $board->seed([
            new Cell(0, 0),
            new Cell(0, 0),
            new Cell(0, 0),
        ]);

        $this->assertCount(3, $board->getLiveCells());
    }

    /** @test */
    public function it_should_create_a_list_of_potential_cells()
    {
        $cell1 = new Cell(0, 0);
        $cell2 = new Cell(1, 0);
        $board = new Board();
        $board->seed([$cell1, $cell2]);

        $realPotentialCells = [
            new Cell(-1, 1), new Cell(0, 1), new Cell(1, 1), new Cell(2, 1),
            new Cell(-1, 0), new Cell(0, 0), new Cell(1, 0), new Cell(2, 0),
            new Cell(-1, -1), new Cell(0, -1), new Cell(1, -1), new Cell(2, -1),
        ];

        $potentials = $board->getPotentialCells();
        $this->assertCount(4 * 3, $potentials);
        $this->assertContainAllOf($potentials, $realPotentialCells);
    }

    /** @test */
    public function it_should_handle_the_blinker_formation()
    {
        $blinker = [new Cell(0, 1), new Cell(1, 1), new Cell(2, 1)];
        $secondBlinker = [new Cell(1, 1), new Cell(1, 0), new Cell(1, 2)];
        $board = new Board();
        $board->seed($blinker);

        $next = $board->spawn();
        $cells = $next->getLiveCells();
        $this->assertCount(3, $cells);

        $this->assertContainAllOf($cells, $secondBlinker);

        $next = $next->spawn();
        $cells = $next->getLiveCells();
        $this->assertContainAllOf($cells, $blinker);
    }

    public function assertContainAllOf($subjects, $objects)
    {
        $containAllObjects = true;
        foreach ($subjects as $subject) {
            if (! in_array($subject, $objects)) {
                $containAllObjects = false;
                break;
            }
        }

        $this->assertTrue($containAllObjects, 'The subject is not fully contained in the object');
    }
}
