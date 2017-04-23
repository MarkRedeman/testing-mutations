<?php

namespace spec\Kata\Conway;

use PhpSpec\ObjectBehavior;
use Kata\Conway\Cell;

class CellSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Kata\Conway\Cell');
    }

    public function let()
    {
        $this->beConstructedWith(0, 0);
    }

    public function it_has_coordinates()
    {
        $this->getX()->shouldReturn(0);
        $this->getY()->shouldReturn(0);
    }

    public function it_can_detect_if_its_a_neighbour()
    {
        $neighbour = new Cell(0, 1);

        $this->isANeighbour($neighbour)->shouldReturn(true);
    }

    public function it_can_detect_if_its_not_a_neighbour()
    {
        $not_a_neighbour = new Cell(0, 2);

        $this->isANeighbour($not_a_neighbour)->shouldReturn(false);
    }

    public function it_is_not_a_neighbour_to_itself()
    {
        $this->isANeighbour($this)->shouldReturn(false);
    }

    public function it_detects_when_it_has_no_neighbours()
    {
        $environment = [];

        $this->neighboursIn($environment)->shouldReturn(0);
    }

    public function it_can_count_neighbours_in_a_list_of_cells()
    {
        $environment = [
            new Cell(0, 0), new Cell(0, 1), new Cell(1, 0),
        ];

        $this->neighboursIn($environment)->shouldReturn(2);
    }

    public function it_can_determine_if_it_will_survive_among_the_other_cells()
    {
        $environment = [
            new Cell(0, 0), new Cell(0, 1), new Cell(1, 0),
        ];

        $this->survivesIn($environment)->shouldReturn(true);
    }

    public function it_dies_if_it_is_alone()
    {
        $environment = [];

        $this->survivesIn($environment)->shouldReturn(false);
    }

    public function it_dies_if_it_has_four_neighbours()
    {
        $environment = [
            new Cell(0, 0), new Cell(0, 1), new Cell(1, 0),
            new Cell(1, 1), new Cell(-1, 0),
        ];

        $this->survivesIn($environment)->shouldReturn(false);
    }

    // function it_survives_if_it_has_two_neighbours()

    public function it_will_not_spawn_if_it_has_two_neighbours()
    {
        $environment = [
            new Cell(0, 1), new Cell(1, 0),
        ];

        $this->survivesIn($environment)->shouldReturn(false);
    }

    public function it_will_spawn_if_it_has_three_neighbours()
    {
        $environment = [
            new Cell(0, 1), new Cell(1, 0), new Cell(1, 1),
        ];

        $this->survivesIn($environment)->shouldReturn(true);
    }

    public function it_can_generate_a_list_of_neighbouring_cells()
    {
        $realNeighbours = [
            new Cell(1, 0),
            new Cell(1, -1),
            new Cell(0, -1),
            new Cell(-1, -1),
            new Cell(-1, 0),
            new Cell(-1, 1),
            new Cell(0, 1),
            new Cell(1, 1),
        ];

        $neighbours = $this->getNeighbours();
        $neighbours->shouldHaveCount(8);
        $neighbours->shouldContainAllOf($realNeighbours);
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
