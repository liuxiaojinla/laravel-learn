<?php

namespace App\Services\Wechat\Work\Events;

class ExternalContactAdd extends ExternalContactChange
{
    /**
     * @return bool
     */
    public function isHalf()
    {
        return $this->getChangeType() == ExternalContactChange::ADD_EXTERNAL_CONTACT;
    }

    /**
     * 获取 State
     * @return string
     */
    public function getState()
    {
        return $this->getMessageAttribute('State', '');
    }

    /**
     * 获取 WelcomeCode
     * @return string
     */
    public function getWelcomeCode()
    {
        return $this->getMessageAttribute('WelcomeCode');
    }
}
