<?php

namespace App\Services\Wechat\Official\Events;

class MenuView extends Event
{
    public function getEventKey()
    {
        return $this->getMessageAttribute('EventKey', '');
    }
}
