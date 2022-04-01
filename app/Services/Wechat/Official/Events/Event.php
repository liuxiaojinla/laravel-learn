<?php

namespace App\Services\Wechat\Official\Events;

use App\Services\Wechat\AbstractEvent;

abstract class Event extends AbstractEvent
{
    /**
     * 获取第三方ID
     * @return string
     */
    public function getSuiteId()
    {
        return $this->message['SuiteId'];
    }

    /**
     * 获取AppId
     * @return string
     */
    public function getAppId()
    {
        return $this->message['ToUserName'];
    }

    /**
     * 获取Openid
     * @return string
     */
    public function getOpenid()
    {
        return $this->message['FromUserName'];
    }

    /**
     * 获取事件类型
     * @return string
     */
    public function getEvent()
    {
        return $this->getMessageAttribute('Event', '');
    }

    /**
     * 获取时间戳
     * @return string
     */
    public function getTimeStamp($default = 0)
    {
        return $this->message['TimeStamp'] ?? $this->message['CreateTime'] ?? $default;
    }
}
