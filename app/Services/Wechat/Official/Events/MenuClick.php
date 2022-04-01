<?php

namespace App\Services\Wechat\Official\Events;

class MenuClick extends Event
{
    public function getEventKey()
    {
        return $this->getMessageAttribute('EventKey', '');
    }
}
