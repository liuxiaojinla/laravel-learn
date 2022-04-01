<?php

namespace App\Listeners\Wechat\Work;

use App\Services\Wechat\Work\DepartmentService;
use App\Services\Wechat\Work\Events\PartyChange;

class SyncParty extends AbstractListener
{
    /**
     * @var DepartmentService
     */
    protected $service;

    /**
     * @param DepartmentService $service
     */
    public function __construct(DepartmentService $service)
    {
        $this->service = $service;
    }

    /**
     * 处理事件
     *
     * @param PartyChange $event
     * @return void
     */
    public function handle(PartyChange $event)
    {
        if ($event->isCreateParty() || $event->isUpdateParty()) {
            $this->syncParty($event);
        } elseif ($event->isDeleteParty()) {
            $this->deleteParty($event);
        }
    }

    /**
     * 同步部门信息
     * @param PartyChange $event
     */
    protected function syncParty(PartyChange $event)
    {
        $this->service->syncOfRawData(
            $event->toArray([
                'Id' => 'department_id',
                'Name' => 'name',
                'Department' => 'department',
                'ParentId' => 'parentid',
                'Order' => 'order',
            ])
        );
    }

    /**
     * 删除部门
     * @param PartyChange $event
     */
    protected function deleteParty(PartyChange $event)
    {
        $this->service->deleteByDepartmentIdOnlyLocal($event->getId());
    }
}
