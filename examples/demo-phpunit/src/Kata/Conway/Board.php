<?php

declare(strict_types=1);

namespace Kata\Conway;

class Board
{
    protected $liveCells = [];

    public function spawn()
    {
        $nextGeneration = [];

        $potentials = $this->getPotentialCells();
        $environment = $this->getLiveCells();

        foreach ($potentials as $potentialCell) {
            if ($potentialCell->survivesIn($environment)) {
                $nextGeneration[] = $potentialCell;
            }
        }

        $board = new Board();
        $board->seed($nextGeneration);

        return $board;
    }

    public function getLiveCells()
    {
        return $this->liveCells;
    }

    public function seed(array $liveCells)
    {
        $this->liveCells = $liveCells;
    }

    public function getPotentialCells()
    {
        $potentials = [];

        $potentials = array_merge($potentials, $this->getLiveCells());

        foreach ($this->getLiveCells() as $cell) {
            $neighbours = $cell->getNeighbours();
            $potentials = array_merge($potentials, $neighbours);
        }

        $potentials = array_map('unserialize', array_unique(array_map('serialize', $potentials)));

        return $potentials;
    }
}
