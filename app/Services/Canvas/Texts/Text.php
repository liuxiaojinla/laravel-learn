<?php

namespace App\Services\Canvas\Texts;

use App\Services\Canvas\Canvas;
use App\Services\Canvas\Color;
use App\Services\Canvas\Dimension;
use App\Services\Canvas\Position;
use App\Services\Canvas\Shapes\Shape;

class Text extends Shape
{
    /**
     * @var string
     */
    protected $text;

    /**
     * @var Color
     */
    protected $foregroundColor;

    /**
     * @param string $text
     * @param int $x
     * @param int $y
     */
    public function __construct($text, $x, $y)
    {
        parent::__construct(
            new Dimension(0, 0),
            new Position($x, $y)
        );
        $this->text = $text;

        $this->foregroundColor = new Color();
    }

    /**
     * @param string $text
     * @param int $x
     * @param int $y
     */
    public static function from($text, $x, $y)
    {
        return new static($text, $x, $y);
    }

    /**
     * @param Color $foregroundColor
     */
    public function setForegroundColor(Color $foregroundColor): void
    {
        $this->foregroundColor = $foregroundColor;
    }

    /**
     * @param Canvas $canvas
     * @return void
     */
    public function onDraw(Canvas $canvas)
    {
        $foregroundColor = imagecolorallocate(
            $canvas->getGd(),
            $this->foregroundColor->getRed(),
            $this->foregroundColor->getGreen(),
            $this->foregroundColor->getBlue()
        );

        imagestring($canvas->getGd(), 0, 0, 0, $this->text, $foregroundColor);
    }
}
