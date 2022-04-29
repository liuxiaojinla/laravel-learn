<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller as BaseController;

class Controller extends BaseController
{
    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * 构造器
     */
    public function __construct()
    {
        $this->request = app('request');
    }
}
