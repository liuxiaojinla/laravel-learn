<?php

namespace App\Jobs\Wechat\Official;

use App\Jobs\Wechat\Taskable;
use App\Models\Promise;
use App\Services\Wechat\Official\TemplateMessageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncTemplateMessages implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Taskable;

    /**
     * 任务类型
     */
    const TASK_TYPE = 'sync_official_template_message';

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
        $this->queue = 'wechat_official';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(TemplateMessageService $templateMessageService)
    {
        try {
            $this->promise->setPendingStatus();
            $templateMessageService->syncAll();
            $this->promise->setSucceededStatus();
        } catch (\Throwable $e) {
            $this->promise->setFailedStatus($e->getMessage() . "\n" . $e->getTraceAsString());
        }
    }
}
