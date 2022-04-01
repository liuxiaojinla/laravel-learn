<?php

namespace App\Services\Wechat\Work\Events;

class PartyChange extends ContactChange
{
    /**
     * @return bool
     */
    public function isCreate()
    {
        return $this->isCreateParty();
    }

    /**
     * @return bool
     */
    public function isUpdate()
    {
        return $this->isUpdateParty();
    }

    /**
     * @return bool
     */
    public function isDelete()
    {
        return $this->isDeleteParty();
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->getMessageAttribute('Id');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getMessageAttribute('Name', '');
    }

    /**
     * @return string
     */
    public function getParentId()
    {
        return $this->getMessageAttribute('ParentId', 0);
    }

    /**
     * @return string
     */
    public function getOrder()
    {
        return $this->getMessageAttribute('Order', 0);
    }
}
