<?php

namespace App\Services\Wechat\Work\Events;

class ExternalGroupChatChange extends Event
{
    // 有新增客户群时，回调该事件。收到该事件后，企业可以调用获取客户群详情接口获取客户群详情。
    const CREATE = 'create';

    // 客户群被修改后（群名变更，群成员增加或移除，群公告变更），回调该事件。收到该事件后，企业需要再调用获取客户群详情接口，以获取最新的群详情。
    const UPDATE = 'update';

    // 当客户群被群主解散后，回调该事件
    const DISMISS = 'dismiss';

    /**
     * 获取 ChatId
     * @return string
     */
    public function getChatId()
    {
        return $this->getMessageAttribute('ChatId');
    }

    /**
     * @return bool
     */
    public function isCreate()
    {
        return $this->getChangeType() == self::CREATE;
    }

    /**
     * @return bool
     */
    public function isUpdate()
    {
        return $this->getChangeType() == self::UPDATE;
    }

    /**
     * @return bool
     */
    public function isDismiss()
    {
        return $this->getChangeType() == self::DISMISS;
    }
}
