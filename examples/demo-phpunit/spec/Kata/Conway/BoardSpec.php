<?php

namespace spec\Kata\Conway;

use PhpSpec\ObjectBehavior;
use Kata\Conway\Cell;

class BoardSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Kata\Conway\Board');
    }

    public function it_should_spawn_an_empty_board_if_empty()
    {
        $this->spawn()->getLiveCells()->shouldHaveCount(0);
    }

    public function it_should_be_empty_if_seeded_with_an_empty_list()
    {
        $this->seed([]);
        $this->getLiveCells()->shouldHaveCount(0);
    }

    public function it_is_filled_when_seeded_with_a_list(Cell $cell1, Cell $cell2, Cell $cell3)
    {
        $this->seed([
            $cell1,
            $cell2,
            $cell3,
        ]);

        $this->getLiveCells()->shouldHaveCount(3);
    }

    public function it_should_create_a_list_of_potential_cells()
    {
        $cell1 = new Cell(0, 0);
        $cell2 = new Cell(1, 0);

        $this->seed([$cell1, $cell2]);

        $realPotentialCells = [
            new Cell(-1, 1), new Cell(0, 1), new Cell(1, 1), new Cell(2, 1),
            new Cell(-1, 0), new Cell(0, 0), new Cell(1, 0), new Cell(2, 0),
            new Cell(-1, -1), new Cell(0, -1), new Cell(1, -1), new Cell(2, -1),
        ];

        $potentials = $this->getPotentialCells();
        $potentials->shouldHaveCount(4 * 3);
        $potentials->shouldContainAllOf($realPotentialCells);
    }

    public function it_should_handle_the_blinker_formation()
    {
        $blinker = [new Cell(0, 1), new Cell(1, 1), new Cell(2, 1)];
        $secondBlinker = [new Cell(1, 1), new Cell(1, 0), new Cell(1, 2)];

        $this->seed($blinker);

        $next = $this->spawn();
        $cells = $next->getLiveCells();
        $cells->shouldHaveCount(3);
        $cells->shouldContainAllOf($secondBlinker);

        $next = $next->spawn();
        $cells = $next->getLiveCells();
        $cells->shouldContainAllOf($blinker);
    }

    public function getMatchers()
    {
        return [
            'containAllOf' => function ($subjects, $objects) {
                $containAllObjects = true;
                foreach ($subjects as $subject) {
                    if (! in_array($subject, $objects)) {
                        $containAllObjects = false;
                        break;
                    }
                }
                return $containAllObjects;
            },
        ];
    }
}
