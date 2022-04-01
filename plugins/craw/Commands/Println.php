<?php

namespace Plugins\craw\Commands;

trait Println
{
    protected function line(...$args)
    {
        foreach ($args as $arg) {
            echo $arg;
        }
        echo "\n";
    }
}
