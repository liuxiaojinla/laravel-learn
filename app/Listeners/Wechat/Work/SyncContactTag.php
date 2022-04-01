<?php

namespace App\Listeners\Wechat\Work;

use App\Services\Wechat\Work\ContactTagService;
use App\Services\Wechat\Work\Events\ExternalTagChange;

class SyncContactTag extends AbstractListener
{
    /**
     * @var ContactTagService
     */
    protected $service;

    /**
     * @param ContactTagService $service
     */
    public function __construct(ContactTagService $service)
    {
        $this->service = $service;
    }

    /**
     * 处理事件
     *
     * @param ExternalTagChange $event
     * @return void
     */
    public function handle(ExternalTagChange $event)
    {
        if ($event->isCreate() || $event->isUpdate()) {
            $this->syncTag($event);
        } elseif ($event->isDelete()) {
            $this->deleteTag($event);
        }
    }

    /**
     * 同步企业标签
     * @param ExternalTagChange $event
     */
    protected function syncTag(ExternalTagChange $event)
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
     * 删除企业标签
     * @param ExternalTagChange $event
     */
    protected function deleteTag(ExternalTagChange $event)
    {
        $this->service->deleteByDepartmentIdOnlyLocal($event->getId());
    }
}
