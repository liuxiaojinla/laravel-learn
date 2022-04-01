<?php

namespace App\Services\Wechat;

class WechatInvalidConfigException extends WechatException
{
    /**
     * @param string $name
     */
    public function __construct($name = '')
    {
        parent::__construct("Invalid [{$name}] config.", 0);
    }
}
