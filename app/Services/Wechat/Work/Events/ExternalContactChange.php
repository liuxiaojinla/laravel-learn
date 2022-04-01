<?php

namespace App\Services\Wechat\Work\Events;

class ExternalContactChange extends Event
{
    // 授权企业中配置了客户联系功能的成员添加外部联系人时，企业微信服务器会向应用的“指令回调URL”推送该事件
    const ADD_EXTERNAL_CONTACT = 'add_external_contact';

    // 外部联系人添加了配置了客户联系功能且开启了免验证的成员时（此时成员尚未确认添加对方为好友），企业微信服务器会向应用的“指令回调URL”推送该事件
    const ADD_HALF_EXTERNAL_CONTACT = 'add_half_external_contact';

    // 授权企业中配置了客户联系功能的成员编辑外部联系人的备注信息(不包括备注手机号码)或企业标签时，企业微信服务器会向应用的“指令回调URL”推送该事件，但仅修改外部联系人备注手机号时不会触发回调。
    const EDIT_EXTERNAL_CONTACT = 'edit_external_contact';

    // 授权企业中配置了客户联系功能的成员删除外部联系人时，企业微信服务器会向应用的“指令回调URL”推送该事件
    const DEL_EXTERNAL_CONTACT = 'del_external_contact';

    // 授权企业中配置了客户联系功能的成员被外部联系人删除时，企业微信服务器会向应用的“指令回调URL”推送该事件
    const DEL_FOLLOW_USER = 'del_follow_user';

    // 企业将客户分配给新的成员接替后，当客户添加失败时，企业微信服务器会向应用的“指令回调URL”推送该事件
    const TRANSFER_FAIL = 'transfer_fail';

    /**
     * 获取 UserID
     * @return string
     */
    public function getUserID()
    {
        return $this->getMessageAttribute('UserID');
    }

    /**
     * 获取 ExternalUserID
     * @return string
     */
    public function getExternalUserID()
    {
        return $this->getMessageAttribute('ExternalUserID');
    }

    /**
     * 获取时间戳
     * @return string
     */
    public function getCreateTime()
    {
        return $this->message['CreateTime'];
    }

    /**
     * @return bool
     */
    public function isAddExternalContact()
    {
        return $this->getChangeType() == self::ADD_EXTERNAL_CONTACT || $this->isAddHalfExternalContact();
    }

    /**
     * @return bool
     */
    public function isAddHalfExternalContact()
    {
        return $this->getChangeType() == self::ADD_HALF_EXTERNAL_CONTACT;
    }

    /**
     * @return bool
     */
    public function isEditExternalContact()
    {
        return $this->getChangeType() == self::EDIT_EXTERNAL_CONTACT;
    }

    /**
     * @return bool
     */
    public function isDelExternalContact()
    {
        return $this->getChangeType() == self::DEL_EXTERNAL_CONTACT;
    }

    /**
     * @return bool
     */
    public function isDelFollowUser()
    {
        return $this->getChangeType() == self::DEL_FOLLOW_USER;
    }

    /**
     * @return bool
     */
    public function isTransferFail()
    {
        return $this->getChangeType() == self::TRANSFER_FAIL;
    }
}
