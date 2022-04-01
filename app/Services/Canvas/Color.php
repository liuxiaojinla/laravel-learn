<?php

namespace App\Services\Canvas;

class Color
{
    /**
     * @var int
     */
    protected $red = 0;

    /**
     * @var int
     */
    protected $green = 0;

    /**
     * @var int
     */
    protected $blue = 0;

    /**
     * @var int
     */
    protected $alpha = 127;

    /**
     * @param int $red
     * @param int $green
     * @param int $blue
     * @param int $alpha
     */
    public function __construct(int $red = 0, int $green = 0, int $blue = 0, float $opacity = 1)
    {
        $this->red = $red;
        $this->green = $green;
        $this->blue = $blue;

        $alpha = $opacity * 127;
        $alpha = min($alpha, 127);
        $alpha = max($alpha, 0);
        $this->alpha = 127 - $alpha;
    }

    /**
     * @return int
     */
    public function getRed(): int
    {
        return $this->red;
    }

    /**
     * @return int
     */
    public function getGreen(): int
    {
        return $this->green;
    }


    /**
     * @return int
     */
    public function getBlue(): int
    {
        return $this->blue;
    }


    /**
     * @return int
     */
    public function getAlpha(): float
    {
        return $this->alpha;
    }

    public static function fromString($color)
    {
    }
}
