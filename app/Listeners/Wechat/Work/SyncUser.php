<?php

namespace App\Listeners\Wechat\Work;

use App\Services\Wechat\Work\Events\UserChange;
use App\Services\Wechat\Work\UserService;

class SyncUser extends AbstractListener
{
    /**
     * @var UserService
     */
    protected $service;

    /**
     * @param UserService $service
     */
    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    /**
     * 处理事件
     *
     * @param UserChange $event
     * @return void
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle(UserChange $event)
    {
        if ($event->isCreateUser() || $event->isUpdateUser()) {
            $this->syncUser($event);
        } elseif ($event->isDeleteUser()) {
            $this->deleteUser($event);
        }
    }

    /**
     * 同步用户
     * @param UserChange $event
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function syncUser(UserChange $event)
    {
        if ($event->hasMessageAttribute('InfoType')) {
            $user = $event->toArray([
                'UserID' => 'userid',
                'NewUserID' => 'new_userid',
                'OpenUserID' => 'open_userid',
                'Name' => 'name',
                'Department' => 'department',
                'MainDepartment' => 'main_department',
                'IsLeaderInDept' => 'is_leader_in_dept',
                'Mobile' => 'mobile',
                'Position' => 'position',
                'Gender' => 'gender',
                'Email' => 'email',
                'Avatar' => 'avatar',
                'Alias' => 'alias',
                'Telephone' => 'telephone',
                'ExtAttr' => 'extattr',
            ]);
        } else {
            $user = $this->service->getOfQy($event->getUserID());
        }

        $openid = $this->service->getOpenidByUseridOfQy($event->getUserID());
        $this->service->syncOfRawData(
            $user,
            $openid,
            $event->getUserID()
        );
    }

    /**
     * 删除用户
     * @param UserChange $event
     */
    protected function deleteUser(UserChange $event)
    {
        $this->service->deleteByUseridOnlyLocal($event->getUserID());
    }
}
