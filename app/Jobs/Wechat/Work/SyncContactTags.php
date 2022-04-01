<?php

namespace App\Jobs\Wechat\Work;

use App\Foundation\Job\Job;
use App\Jobs\Wechat\Taskable;
use App\Models\Promise;
use App\Services\Wechat\Work\ContactTagService;

class SyncContactTags extends Job
{
    use Taskable;

    /**
     * 任务类型
     */
    const TASK_TYPE = 'sync_work_contact_tags';

    /**
     * @var Promise
     */
    protected $promise;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Promise $promise)
    {
        $this->promise = $promise;
        $this->queue = 'wechat_work';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ContactTagService $contactTagService)
    {
        try {
            $this->promise->setPendingStatus();
            $contactTagService->syncAll();
            $this->promise->setSucceededStatus();
        } catch (\Throwable $e) {
            $this->promise->setFailedStatus($e->getMessage() . "\n" . $e->getTraceAsString());
        }
    }
}
