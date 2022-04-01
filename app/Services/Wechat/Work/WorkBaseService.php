<?php

namespace App\Services\Wechat\Work;

use App\Services\Wechat\BaseService;

class WorkBaseService extends BaseService
{
    /**
     * @var string
     */
    private $corpId;

    /**
     * @var string
     */
    private $agentId;

    /**
     * @return string
     */
    protected function corpId()
    {
        if (!$this->corpId) {
            $this->corpId = $this->work()->config['corp_id'];
        }

        return $this->corpId;
    }

    /**
     * @return string
     */
    protected function agentId()
    {
        if (!$this->agentId) {
            $this->agentId = $this->work()->config['agent_id'];
        }

        return $this->agentId;
    }
}
