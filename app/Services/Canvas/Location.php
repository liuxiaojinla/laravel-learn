<?php

namespace App\Services\Canvas;

class Location
{
    /**
     * @var int
     */
    protected $left;

    /**
     * @var int
     */
    protected $top;

    /**
     * @var int
     */
    protected $right;

    /**
     * @var int
     */
    protected $bottom;

    /**
     * @param int $left
     * @param int $top
     * @param int $right
     * @param int $bottom
     */
    public function __construct(int $left, int $top, int $right, int $bottom)
    {
        $this->left = $left;
        $this->top = $top;
        $this->right = $right;
        $this->bottom = $bottom;
    }

    /**
     * @return int
     */
    public function getLeft(): int
    {
        return $this->left;
    }

    /**
     * @return int
     */
    public function getTop(): int
    {
        return $this->top;
    }

    /**
     * @return int
     */
    public function getRight(): int
    {
        return $this->right;
    }

    /**
     * @return int
     */
    public function getBottom(): int
    {
        return $this->bottom;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return abs($this->right - $this->left);
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return abs($this->bottom - $this->top);
    }
}