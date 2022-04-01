<?php

namespace App\Services\Wechat\Work\Events;

class ExternalContactDelete extends ExternalContactChange
{
    /**
     * 删除客户的操作来源，DELETE_BY_TRANSFER表示此客户是因在职继承自动被转接成员删除
     * @return string
     */
    public function getSource()
    {
        return $this->getMessageAttribute('Source', '');
    }
}
