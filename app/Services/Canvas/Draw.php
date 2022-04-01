<?php

namespace App\Services\Canvas;

interface Draw
{
    /**
     * @param Canvas $canvas
     */
    public function onDraw(Canvas $canvas);
}
