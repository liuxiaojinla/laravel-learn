<?php

namespace App\Services;

use Illuminate\Contracts\Support\Renderable;

abstract class Weight implements Renderable
{
    protected $view;

    public function __construct()
    {
        $this->view = app('view');
    }

    protected function fetch($view, $data = [], $mergeData = [])
    {
        return $this->view->make($view, $data, $mergeData);
    }
}
