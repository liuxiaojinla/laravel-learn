<?php

namespace App\Services\Wechat\Work\Events;

class UserChange extends ContactChange
{
    /**
     * @return bool
     */
    public function isCreate()
    {
        return $this->isCreateUser();
    }

    /**
     * @return bool
     */
    public function isUpdate()
    {
        return $this->isUpdateUser();
    }

    /**
     * @return bool
     */
    public function isDelete()
    {
        return $this->isDeleteUser();
    }

    /**
     * @return string
     */
    public function getUserID()
    {
        return $this->getMessageAttribute('UserID');
    }

    /**
     * @return string
     */
    public function getOpenUserID()
    {
        return $this->getMessageAttribute('OpenUserID', '');
    }

}
