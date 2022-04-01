<?php

namespace App\Services\Wechat\Work\Events;

class ExternalTagChange extends Event
{
    // 企业/管理员创建客户标签/标签组时，企业微信服务器将向具有企业客户权限的第三方应用指令回调URL回调此事件。收到该事件后，第三方服务商需要调用获取企业标签库来获取标签/标签组的详细信息。
    const CREATE = 'create';

    // 当企业客户标签/标签组被修改时，企业微信服务器将向具有企业客户权限的第三方应用指令回调URL回调此事件。收到该事件后，第三方服务商需要调用获取企业标签库来获取标签/标签组的详细信息。
    const UPDATE = 'update';

    // 当企业客户标签/标签组被删除改时，企业微信服务器将向具有企业客户权限的第三方应用指令回调URL回调此事件。删除标签组时，该标签组下的所有标签将被同时删除，但不会进行回调。
    const DELETE = 'delete';

    // 当企业管理员在终端/管理端调整标签顺序时，可能导致标签顺序整体调整重排，引起大部分标签的order值发生变化，此时会回调此事件，收到此事件后企业应尽快全量同步标签的order值，防止后续调用接口排序出现非预期结果。
    const SHUFFLE = 'shuffle';

    /**
     * 获取 ChatId
     * @return string
     */
    public function getTagId()
    {
        return $this->getMessageAttribute('Id');
    }

    /**
     * 获取 TagType
     * @return string
     */
    public function getTagType()
    {
        return $this->getMessageAttribute('TagType');
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
    public function isDelete()
    {
        return $this->getChangeType() == self::DELETE;
    }

    /**
     * @return bool
     */
    public function isShuffle()
    {
        return $this->getChangeType() == self::SHUFFLE;
    }
}
