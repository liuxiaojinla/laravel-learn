<?php

namespace App\Jobs\Wechat\Work;

use App\Foundation\Job\Job;
use App\Jobs\Wechat\Taskable;
use App\Models\Promise;
use App\Services\Wechat\Work\ContactService;

class SyncContacts extends Job
{
    use Taskable;

    /**
     * 任务类型
     */
    const TASK_TYPE = 'sync_work_contacts';

    /**
     * @var Promise
     */
    protected $promise;

    /**
     * @var array
     */
    protected $items;

    /**
     * @var ContactService
     */
    protected $contactService;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Promise $promise, array $items = null)
    {
        $this->promise = $promise;
        $this->items = $items;
        $this->queue = 'wechat_work';
    }

    /**
     * Execute the job.
     *
     * @param ContactService $contactService
     * @return void
     * @throws \Throwable
     */
    public function handle(ContactService $contactService)
    {
        $this->contactService = $contactService;
        if ($this->items) {
            $e = $this->syncItems();

            $progress = $this->promise->setProgressByIncCount(count($this->items));
            if ($progress >= 100) {
                $this->promise->setSucceededStatus([
                    'actual_quantity' => $this->promise->getProgressCount(),
                ]);
            } else {
                $this->promise->forceFill([
                    'actual_quantity' => $this->promise->getProgressCount(),
                ])->save();
            }

            if ($e) {
                throw $e;
            }
        } else {
            $this->chunk();
        }
    }

    /**
     * @return \Exception|\Throwable|null
     */
    protected function syncItems()
    {
        try {
            $this->contactService->syncOfExternalUseridList($this->items);
        } catch (\Throwable $e) {
            return $e;
        }

        return null;
    }

    /**
     * 分块同步
     */
    protected function chunk()
    {
        try {
            $this->promise->setPendingStatus();

            $externalUserIds = $this->contactService->getAllExternalUseridOfQy();
            $count = count($externalUserIds);

            $this->promise->forceFill([
                'expect_quantity' => $count,
            ])->save();

            $this->promise->setProgressByIncCount(0);

            $externalUserIds = array_chunk($externalUserIds, 100);
            foreach ($externalUserIds as $items) {
                static::dispatch($this->promise, $items)->delay(rand(0, 2));
            }
        } catch (\Throwable $e) {
            $this->promise->setFailedStatus($e->getMessage() . "\n" . $e->getTraceAsString());
        }
    }
}
