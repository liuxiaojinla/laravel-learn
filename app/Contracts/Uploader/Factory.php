<?php

namespace App\Contracts\Uploader;

interface Factory
{
    /**
     * @param string $scene
     * @return \App\Contracts\Uploader\Uploader
     */
    public function uploader($scene = null);
}