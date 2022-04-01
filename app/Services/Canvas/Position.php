<?php

namespace App\Services\Canvas;

class Position
{
    /**
     * @var float
     */
    protected $x;

    /**
     * @var float
     */
    protected $y;

    /**
     * @param float $x
     * @param float $y
     */
    public function __construct(float $x, float $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    /**
     * @return float
     */
    public function getX(): float
    {
        return $this->x;
    }

    /**
     * @param float $x
     * @return $this
     */
    public function setX(float $x)
    {
        $this->x = $x;

        return $this;
    }

    /**
     * @return float
     */
    public function getY(): float
    {
        return $this->y;
    }

    /**
     * @param float $y
     * @return $this
     */
    public function setY(float $y)
    {
        $this->y = $y;

        return $this;
    }

    /**
     * @param float $x
     * @param float $y
     * @return $this
     */
    public function setXY(float $x, float $y)
    {
        $this->setX($x);
        $this->setY($y);

        return $this;
    }
}
