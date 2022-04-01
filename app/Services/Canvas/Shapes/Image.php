<?php

namespace App\Services\Canvas\Shapes;

use App\Services\Canvas\Canvas;
use App\Services\Canvas\Dimension;
use App\Services\Canvas\Location;
use App\Services\Canvas\Position;

class Image extends Shape
{
    /**
     * @var \GdImage
     */
    protected $gd;

    /**
     * @var string
     */
    protected $mode;

    /**
     * @var Location
     */
    protected $borderRadius;

    /**
     * @param \GdImage $gd
     */
    protected function __construct($gd)
    {
        parent::__construct(
            new Dimension(imagesx($gd), imagesy($gd)),
            new Position(0, 0)
        );

        $this->gd = $gd;
        $this->borderRadius = new Location(0, 0, 0, 0);
    }

    /**
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @param mixed $mode
     */
    public function setMode($mode): void
    {
        $this->mode = $mode;
    }

    /**
     * @param string $path
     * @return static
     */
    public static function fromPath($path)
    {
        return new static(imagecreatefrompng($path));
    }

    /**
     * @param string $stream
     * @return static
     */
    public static function fromStream($stream)
    {
        return new static(imagecreatefromstring($stream));
    }

    /**
     * @inheritDoc
     */
    public function onDraw(Canvas $canvas)
    {
        $rect = $this->getLocation();
        imagecopy(
            $canvas->getGd(),
            $this->gd,
            $rect->getLeft(),
            $rect->getTop(),
            0,
            0,
            $rect->getWidth(),
            $rect->getHeight()
        );
    }
}
