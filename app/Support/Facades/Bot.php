<?php

namespace App\Support\Facades;

use App\Services\Bot\BotManager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static bool sendMessage(array $message)
 * @method static bool sendTextMessage(string $string)
 * @see BotManager
 */
class Bot extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'bot';
    }
}