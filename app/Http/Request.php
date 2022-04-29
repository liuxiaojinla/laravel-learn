<?php

namespace App\Http;

use Illuminate\Http\Request as BaseRequest;

class Request extends BaseRequest
{
    public function hello()
    {
        return "hello world";
    }
}