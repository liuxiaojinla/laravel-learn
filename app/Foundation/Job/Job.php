<?php

namespace App\Foundation\Job;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class Job implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    use SerializesModels {
        SerializesModels::__serialize as SerializesModels__serialize;
        SerializesModels::__unserialize as SerializesModels__unserialize;
    }

    use InteractsCorpContext;

    /**
     * 序列化数据
     * @return array
     */
    public function __serialize()
    {
        $this->onSerializeCorpId();

        return $this->SerializesModels__serialize();
    }

    /**
     * 反序列化数据
     * @param array $values
     * @return void
     */
    public function __unserialize(array $values)
    {
        $this->SerializesModels__unserialize($values);

        $this->onUnserializeCorpId();
    }
}
