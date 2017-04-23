<?php

namespace Kata\Conway;

class Cell
{
    protected static $survivesWithValues = [2, 3];
    protected static $spawnsWithValues = [3];

    protected $x;
    protected $y;

    public function __construct($x, $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    public function getX()
    {
        return $this->x;
    }

    public function getY()
    {
        return $this->y;
    }

    public function isANeighbour($neighbour)
    {
        return in_array($neighbour, $this->getNeighbours());
    }

    public function equals($other)
    {
        return $this->getX() === $other->getX() && $this->getY() === $other->getY();
    }

    public function neighboursIn($environment)
    {
        $neighbours = 0;

        foreach ($environment as $neighbour) {
            if ($this->isANeighbour($neighbour)) {
                $neighbours++;
            }
        }
        return $neighbours;
    }

    public function survivesIn($environment)
    {
        $neighbours = $this->neighboursIn($environment);

        if (in_array($this, $environment)) {
            return in_array($neighbours, self::$survivesWithValues);
        }

        return in_array($neighbours, self::$spawnsWithValues);
    }

    public function getNeighbours()
    {
        return [
            new self($this->getX() - 1, $this->getY()),
            new self($this->getX() + 1, $this->getY() - 1),
            new self($this->getX(), $this->getY() - 1),
            new self($this->getX() - 1, $this->getY() - 1),
            new self($this->getX() - 1, $this->getY()),
            new self($this->getX() - 1, $this->getY() + 1),
            new self($this->getX(), $this->getY() + 1),
            new self($this->getX() + 1, $this->getY() + 1),
        ];
    }
}
