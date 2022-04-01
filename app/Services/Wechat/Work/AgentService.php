<?php

namespace App\Services\Wechat\Work;

use App\Models\Wechat\WechatWorkAgent;
use App\Services\Wechat\WechatResult;

class AgentService extends WorkBaseService
{
    /**
     * @var string
     */
    protected $model = WechatWorkAgent::class;

    /**
     * @param array $data
     * @return WechatWorkAgent
     */
    public function syncOfRawData($data)
    {
        return WechatWorkAgent::unguarded(function () use ($data) {
            $data = WechatWorkAgent::getAllowFields($data);
            $data['corp_id'] = $this->corpId();

            return WechatWorkAgent::query()->updateOrCreate([
                'corp_id' => $data['corp_id'],
                'agentid' => $data['agentid'],
            ], $data);
        });
    }

    /**
     * @param string $authCorpId
     * @param string $agentid
     */
    public function deleteOnLocal($authCorpId, $agentid)
    {
        WechatWorkAgent::query()->where([
            'corp_id' => $authCorpId,
            'agentid' => $agentid,
        ])->delete();
    }

    /**
     * @return WechatWorkAgent|null
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function sync()
    {
        $data = $this->getOfQy();
        if (empty($data)) {
            return null;
        }

        return $this->syncOfRawData($data);
    }

    /**
     * @return array|null
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function getOfQy()
    {
        $result = WechatResult::make($this->work()->agent->get($this->agentId()));

        return $result->isSucceeded() ? $result->toArray() : null;
    }

    /**
     * @param string $corpId
     * @param string $agentId
     * @return \EasyWeChat\Work\Application
     */
    public function newWork($corpId, $agentId)
    {
        $info = WechatWorkAgent::query()->where('corp_id', $corpId)->where('agentid', $agentId)->firstOrFail();

        $this->getWechatFactory()->setConfig('works.default', [
            'corp_id' => $info->corp_id,
            'agent_id' => $info->agentid,
            'secret' => $info->permanent_code,
            'token' => $info->token,
            'aes_key' => $info->aes_key,
        ]);

        return $this->getWechatFactory()->work();
    }
}
