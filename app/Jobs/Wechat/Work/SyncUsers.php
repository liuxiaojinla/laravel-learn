<?php

namespace App\Jobs\Wechat\Work;

use App\Foundation\Job\Job;
use App\Jobs\Wechat\Taskable;
use App\Models\Promise;
use App\Models\Wechat\WechatWorkUser;
use App\Services\Wechat\Work\UserService;

class SyncUsers extends Job
{
    use Taskable;

    /**
     * 任务类型
     */
    const TASK_TYPE = 'sync_work_users';

    /**
     * @var Promise
     */
    protected $promise;

    /**
     * @var string
     */
    protected $batchNo;

    /**
     * @var array
     */
    protected $items;

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Promise $promise, array $items = null, $batchNo = null)
    {
        $this->queue = 'wechat_work';
        $this->promise = $promise;
        $this->items = $items;
        $this->batchNo = $batchNo;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Throwable
     */
    public function handle(UserService $userService)
    {
        $this->userService = $userService;

        if ($this->items) {
            $this->userService->startBatch($this->batchNo);

            $exception = $this->syncItems();

            $progress = $this->promise->setProgressByIncCount(count($this->items));
            if ($progress >= 100) {
                $this->promise->setSucceededStatus([
                    'actual_quantity' => $this->promise->getProgressCount(),
                ]);
                $this->userService->completeBatch(function ($batchNo) {
                    WechatWorkUser::query()->where('corp_id', $this->corpId)
                        ->where('sync_batch_no', '<>', $batchNo)->delete();
                });
            } else {
                $this->promise->forceFill([
                    'actual_quantity' => $this->promise->getProgressCount(),
                ])->save();
            }

            if ($exception) {
                throw $exception;
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
            $this->userService->syncOfRawItems($this->items);
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

            $this->batchNo = $this->userService->startBatch();
            $users = $this->userService->getAllOfQy();
            $count = count($users);

            $this->promise->forceFill([
                'expect_quantity' => $count,
            ])->save();

            $this->promise->setProgressByIncCount(0);

            $users = array_chunk($users, 100);
            foreach ($users as $items) {
                static::dispatch($this->promise, $items, $this->batchNo);
            }
        } catch (\Throwable $e) {
            $this->promise->setFailedStatus($e->getMessage() . "\n" . $e->getTraceAsString());
        }
    }
}
