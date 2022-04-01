<?php

namespace App\Foundation\Controller;

use Illuminate\Support\Facades\Request;

trait PageCURD
{
    use CURD;

    /**
     * @inerhitDoc
     */
    protected function renderIndex($data)
    {
        return view($this->parsePath('index'), [
            'data' => $data,
        ]);
    }

    /**
     * @inerhitDoc
     */
    protected function renderDetail($info)
    {
        return view($this->parsePath('detail'), [
            'info' => $info,
        ]);
    }

    /**
     * @inerhitDoc
     */
    protected function renderShow($info)
    {
        return view($this->parsePath('show'), [
            'info' => $info,
        ]);
    }

    /**
     * @param string $scene
     * @return string
     */
    protected function parsePath($scene)
    {
        $path = ltrim(Request::path(), '/');
        $paths = $path ? explode('/', $path, 3) : [];

        $paths[0] = $paths[0] ?? 'index';
        $paths[1] = $paths[1] ?? $scene;
        if (isset($paths[2])) {
            $paths[2] = str_replace('/', '_', $paths[2]);
        }
        array_unshift($paths, Request::module());

        return implode('.', $paths);
    }
}
