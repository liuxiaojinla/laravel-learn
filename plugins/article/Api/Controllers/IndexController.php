<?php

namespace Plugins\article\Api\Controllers;

use App\Support\Facades\Hint;

class IndexController
{
    public function index()
    {
        return Hint::success('Hello Plugin Api');
    }
}