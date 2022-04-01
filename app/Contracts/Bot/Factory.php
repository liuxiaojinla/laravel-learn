<?php

namespace App\Contracts\Bot;

interface Factory
{
    /**
     * 选择机器人
     * @param string $name
     * @return Bot
     */
    public function bot($name): Bot;
}