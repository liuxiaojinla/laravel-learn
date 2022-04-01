<?php

namespace App\Services\Canvas\Shapes;

use App\Services\Canvas\Color;
use App\Services\Canvas\Dimension;
use App\Services\Canvas\Draw;
use App\Services\Canvas\Location;
use App\Services\Canvas\Position;
use App\Services\Canvas\Shape as ShapeContract;

abstract class Shape implements ShapeContract, Draw
{
    /**
     * @var Dimension
     */
    protected $size;

    /**
     * @var Position
     */
    protected $position;

    /**
     * @var Color
     */
    protected $borderColor;

    /**
     * @var Color
     */
    protected $backgroundColor;

    /**
     * @param Dimension $size
     * @param Position $position
     * @param Color|null $borderColor
     * @param Color|null $backgroundColor
     */
    public function __construct(Dimension $size, Position $position, ?Color $borderColor = null, ?Color $backgroundColor = null)
    {
        $this->setSize($size);
        $this->setPosition($position);
        $this->setBorderColor($borderColor);
        $this->setBackgroundColor($backgroundColor);
    }

    /**
     * @inheritDoc
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @inheritDoc
     */
    public function setSize(Dimension $size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @inheritDoc
     */
    public function setPosition(Position $position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getBorderColor(): Color
    {
        return $this->borderColor;
    }

    /**
     * @inheritDoc
     */
    public function setBorderColor(?Color $color)
    {
        $this->borderColor = $color;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getBackgroundColor()
    {
        return $this->backgroundColor;
    }

    /**
     * @inheritDoc
     */
    public function setBackgroundColor(?Color $color)
    {
        $this->backgroundColor = $color;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getLocation()
    {
        return new Location(
            $this->position->getX(),
            $this->position->getY(),
            $this->position->getX() + $this->size->getWidth(),
            $this->position->getY() + $this->size->getHeight(),
        );
    }
}
