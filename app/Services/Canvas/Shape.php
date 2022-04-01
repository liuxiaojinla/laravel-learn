<?php

namespace App\Services\Canvas;

interface Shape
{
    /**
     * @return Dimension
     */
    public function getSize();

    /**
     * @param Dimension $size
     */
    public function setSize(Dimension $size);

    /**
     * @return Location
     */
    public function getPosition();

    /**
     * @param Position $position
     */
    public function setPosition(Position $position);

    /**
     * @return Location
     */
    public function getLocation();

    /**
     * @return Color
     */
    public function getBorderColor();

    /**
     * @param Color|null $color
     */
    public function setBorderColor(?Color $color);

    /**
     * @return Location
     */
    public function getBackgroundColor();

    /**
     * @param Color|null $color
     */
    public function setBackgroundColor(?Color $color);
}
