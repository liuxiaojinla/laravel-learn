<?php

namespace App\Foundation\Job;

use App\Services\CorpContext;

trait InteractsCorpContext
{
    /**
     * @var string
     */
    protected $corpId;

    /**
     * @return string
     */
    protected function getCorpId()
    {
        return $this->corpId;
    }

    /**
     * @return void
     */
    protected function onSerializeCorpId()
    {
        if (empty($this->corpId)) {
            $this->corpId = CorpContext::instance()->getCorpId();
        }
    }

    /**
     * @return void
     */
    protected function onUnserializeCorpId()
    {
        if (!empty($this->corpId)) {
            CorpContext::use($this->corpId, true);
        }
    }

    /**
     * @return \App\Services\CorpContext
     */
    protected function corpContext()
    {
        return CorpContext::instance();
    }
}
