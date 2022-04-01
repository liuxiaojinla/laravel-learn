<?php

namespace App\Contracts\Plugin;

interface Plugin
{
    public function boot();

    public function install();

    public function uninstall();

    public function upgrade();

    public function getInfo();
}
