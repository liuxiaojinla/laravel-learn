<?php

namespace App\Services\Wechat\Official\Events;

class UserSubscribe extends Event
{
    // 订阅
    const SUBSCRIBE = 'subscribe';

    // 取消订阅
    const UNSUBSCRIBE = 'unsubscribe';

    // 用户已关注时扫描带参数二维码
    const SCAN = 'SCAN';

    public function getEventKey()
    {
        return $this->getMessageAttribute('EventKey', '');
    }

    public function getTicket()
    {
        return $this->getMessageAttribute('Ticket', '');
    }

    public function isSubscribe()
    {
        return self::SUBSCRIBE == $this->getEvent();
    }

    public function isSubscribed()
    {
        return $this->isSCAN();
    }

    public function isUnsubscribe()
    {
        return self::UNSUBSCRIBE == $this->getEvent();
    }

    public function isSCAN()
    {
        return self::SCAN == $this->getEvent();
    }
}
