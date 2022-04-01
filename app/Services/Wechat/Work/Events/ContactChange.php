<?php

namespace App\Services\Wechat\Work\Events;

class ContactChange extends Event
{
    const CREATE_USER = 'create_user';

    const UPDATE_USER = 'update_user';

    const DELETE_USER = 'delete_user';

    const CREATE_PARTY = 'create_party';

    const UPDATE_PARTY = 'update_party';

    const DELETE_PARTY = 'delete_party';

    const UPDATE_TAG = 'update_tag';

    /**
     * @return bool
     */
    public function isCreateUser()
    {
        return $this->getChangeType() == self::CREATE_USER;
    }

    /**
     * @return bool
     */
    public function isUpdateUser()
    {
        return $this->getChangeType() == self::UPDATE_USER;
    }

    /**
     * @return bool
     */
    public function isDeleteUser()
    {
        return $this->getChangeType() == self::DELETE_USER;
    }

    /**
     * @return bool
     */
    public function isCreateParty()
    {
        return $this->getChangeType() == self::CREATE_PARTY;
    }

    /**
     * @return bool
     */
    public function isUpdateParty()
    {
        return $this->getChangeType() == self::UPDATE_PARTY;
    }

    /**
     * @return bool
     */
    public function isDeleteParty()
    {
        return $this->getChangeType() == self::DELETE_PARTY;
    }

    /**
     * @return bool
     */
    public function isUpdateTag()
    {
        return $this->getChangeType() == self::DELETE_PARTY;
    }
}
