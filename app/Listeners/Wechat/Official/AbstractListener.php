<?php

namespace App\Listeners\Wechat\Official;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

abstract class AbstractListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * @var string
     */
    public $queue = 'wechat_official';

    /**
     * 任务可尝试次数
     *
     * @var int
     */
    public $tries = 3;
}
