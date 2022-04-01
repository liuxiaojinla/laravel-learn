<?php

namespace App\Services\Wechat\Work\Events;

class ExternalGroupChatUpdate extends ExternalGroupChatChange
{
    // 成员入群
    const ADD_MEMBER = 'add_member';

    // 成员退群
    const DEL_MEMBER = 'del_member';

    // 群主变更
    const CHANGE_OWNER = 'change_owner';

    // 群名变更
    const CHANGE_NAME = 'change_name';

    // 群公告变更
    const CHANGE_NOTICE = 'change_notice';

    /**
     * 获取变更详情标识
     * add_member : 成员入群
     * del_member : 成员退群
     * change_owner : 群主变更
     * change_name : 群名变更
     * change_notice : 群公告变更
     * @return string
     */
    public function getUpdateDetail()
    {
        return $this->getMessageAttribute('UpdateDetail');
    }

    /**
     * @return bool
     */
    public function isAddMember()
    {
        return $this->getUpdateDetail() == self::ADD_MEMBER;
    }

    /**
     * @return bool
     */
    public function isDelMember()
    {
        return $this->getUpdateDetail() == self::DEL_MEMBER;
    }

    /**
     * @return bool
     */
    public function isChangeOwner()
    {
        return $this->getUpdateDetail() == self::CHANGE_OWNER;
    }

    /**
     * @return bool
     */
    public function isChangeName()
    {
        return $this->getUpdateDetail() == self::CHANGE_NAME;
    }

    /**
     * @return bool
     */
    public function isChangeNotice()
    {
        return $this->getUpdateDetail() == self::CHANGE_NOTICE;
    }

    /**
     * 获取加入群聊场景值
     * @return string
     */
    public function getJoinScene()
    {
        return $this->getMessageAttribute('JoinScene', 0);
    }

    /**
     * 获取退出群聊场景值
     * @return string
     */
    public function getQuitScene()
    {
        return $this->getMessageAttribute('JoinScene', 0);
    }

    /**
     * 获取退出群聊场景值
     * @return string
     */
    public function getMemChangeCnt()
    {
        return $this->getMessageAttribute('MemChangeCnt', 0);
    }
}
