<?php

namespace App\Contracts\Pay;

interface Gateway
{
    public function pay($attributes = []);
}