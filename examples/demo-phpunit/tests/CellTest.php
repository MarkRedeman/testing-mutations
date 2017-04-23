<?php

namespace Test\Kata\Conway;

use PHPUnit_Framework_TestCase;
use Kata\Conway\Cell;

class CellTest extends PHPUnit_Framework_TestCase
{
    private $cell;

    public function setUp()
    {
        $this->cell = new Cell(0, 0);
    }

    /** @test */
    public function it_has_coordinates()
    {
        $this->assertEquals($this->cell->getX(), 0);
        $this->assertEquals($this->cell->getY(), 0);
    }

    /** @test */
    public function it_can_detect_if_its_a_neighbour()
    {
        $neighbour = new Cell(0, 1);

        $this->assertTrue($this->cell->isANeighbour($neighbour));
    }

    /** @test */
    public function it_can_detect_if_its_not_a_neighbour()
    {
        $not_a_neighbour = new Cell(0, 2);

        $this->assertFalse($this->cell->isANeighbour($not_a_neighbour));
    }

    /** @test */
    public function it_is_not_a_neighbour_to_itself()
    {
        $this->assertFalse($this->cell->isANeighbour($this));

    }

    /** @test */
    public function it_detects_when_it_has_no_neighbours()
    {
        $environment = [];

        $this->assertEquals(0, $this->cell->neighboursIn($environment));
    }

    /** @test */
    public function it_can_count_neighbours_in_a_list_of_cells()
    {
        $environment = [
            new Cell(0, 0), new Cell(0, 1), new Cell(1, 0),
        ];

        // $this->assertEquals(2, $this->cell->neighboursIn($environment));
    }

    /** @test */
    public function it_can_determine_if_it_will_survive_among_the_other_cells()
    {
        $environment = [
            new Cell(0, 0), new Cell(0, 1), new Cell(1, 0),
        ];

        $this->assertTrue($this->cell->survivesIn($environment));
    }

    /** @test */
    public function it_dies_if_it_is_alone()
    {
        $environment = [];

        $this->assertFalse($this->cell->survivesIn($environment));
    }

    /** @test */
    public function it_dies_if_it_has_four_neighbours()
    {
        $environment = [
            new Cell(0, 0), new Cell(0, 1), new Cell(1, 0),
            new Cell(1, 1), new Cell(-1, 0),
        ];

        $this->assertFalse($this->cell->survivesIn($environment));
    }

    // function it_survives_if_it_has_two_neighbours()

    /** @test */
    public function it_will_not_spawn_if_it_has_two_neighbours()
    {
        $environment = [
            new Cell(0, 1), new Cell(1, 0),
        ];

        $this->assertFalse($this->cell->survivesIn($environment));
    }

    /** @test */
    public function it_will_spawn_if_it_has_three_neighbours()
    {
        $environment = [
            new Cell(0, 1), new Cell(1, 0), new Cell(1, 1),
        ];

        $this->assertTrue($this->cell->survivesIn($environment));
    }

    /** @test */
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

        $neighbours = $this->cell->getNeighbours();
        $this->assertCount(8, $neighbours);
        $this->assertContainAllOf($neighbours, $realNeighbours);
    }

    /** @test */
    public function cells_can_be_equal()
    {
        $this->assertTrue((new Cell(0, 0))->equals(new Cell(0, 0)));
        $this->assertFalse((new Cell(1, 0))->equals(new Cell(0, 0)));
        $this->assertFalse((new Cell(0, 1))->equals(new Cell(0, 0)));
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
