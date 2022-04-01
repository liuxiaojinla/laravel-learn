<?php

namespace App\Services\Wechat\Work\Events;

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
     * 获取CorpId
     * @return string
     */
    public function getAuthCorpId()
    {
        return $this->message['AuthCorpId'];
    }

    /**
     * 获取CorpId
     * @return string
     */
    public function getCorpId()
    {
        return $this->message['AuthCorpId'] ?? $this->message['ToUserName'] ?? '';
    }

    /**
     * 获取消息类型
     * @return string
     */
    public function getInfoType()
    {
        return $this->message['InfoType'] ?? '';
    }

    /**
     * 获取变更类型
     * @return string
     */
    public function getChangeType()
    {
        return $this->message['ChangeType'] ?? '';
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
