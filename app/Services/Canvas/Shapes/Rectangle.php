<?php

namespace App\Services\Canvas\Shapes;

use App\Services\Canvas\Canvas;

class Rectangle extends Shape
{
    /**
     * @inheritDoc
     */
    public function onDraw(Canvas $canvas)
    {
        $rect = $this->getLocation();

        if ($this->backgroundColor) {
            $backgroundColor = imagecolorallocate(
                $canvas->getGd(),
                $this->backgroundColor->getRed(),
                $this->backgroundColor->getGreen(),
                $this->backgroundColor->getBlue()
            );
            imagefilledrectangle(
                $canvas->getGd(),
                $rect->getLeft(),
                $rect->getTop(),
                $rect->getRight(),
                $rect->getBottom(),
                $backgroundColor
            );
        }

        if ($this->borderColor) {
            $borderColor = imagecolorallocate(
                $canvas->getGd(),
                $this->borderColor->getRed(),
                $this->borderColor->getGreen(),
                $this->borderColor->getBlue()
            );
            imagerectangle(
                $canvas->getGd(),
                $rect->getLeft(),
                $rect->getTop(),
                $rect->getRight(),
                $rect->getBottom(),
                $borderColor
            );
        }
    }
}
