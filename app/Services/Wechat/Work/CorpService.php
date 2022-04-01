<?php

namespace App\Services\Wechat\Work;

use App\Models\Wechat\WechatWorkAgent;
use App\Models\Wechat\WechatWorkCorp;
use App\Models\Wechat\WechatWorkUser;
use App\Services\Wechat\WechatResult;
use App\Support\WeWork;
use Illuminate\Database\Eloquent\Collection;

class CorpService
{
    /**
     * 同步企业信息
     * @param array $data
     * @return WechatWorkCorp
     */
    public function syncOfRawData($data)
    {
        $data['corp_id'] = $data['corpid'];

        return WechatWorkCorp::unguarded(function () use ($data) {
            $data = WechatWorkCorp::getAllowFields($data);
            $data['deleted_at'] = null;

            return WechatWorkCorp::withTrashed()->updateOrCreate([
                'corp_id' => $data['corp_id'],
            ], $data);
        });
    }

    /**
     * 同步应用列表
     * @param string $corpId
     * @param array $agents
     * @return array
     */
    public function syncAgentsOfRawAgents($corpId, array $agents)
    {
        $result = [];

        foreach ($agents as $agent) {
            $result[] = WechatWorkAgent::unguarded(function () use ($corpId, $agent) {
                $isCustomizedApp = $agent['is_customized_app'] ?? 0;
                $saveData = array_merge([
                    'secret' => '',
                    'auth_type' => $isCustomizedApp ? 2 : 1,
                    'auth_mode' => 0,
                    'square_logo_url' => '',
                    'description' => '',
                    'privilege' => [],
                    'level' => 0,
                ], WechatWorkAgent::getAllowFields($agent));
                $saveData['level'] = $agent['privilege']['level'] ?? 0;

                return WechatWorkAgent::query()->updateOrCreate([
                    'corp_id' => $corpId,
                    'agentid' => $agent['agentid'],
                ], $saveData);
            });
        }

        return $result;
    }

    /**
     * 初始化用户
     * @param string $corpId
     * @param array $authUserInfo
     * @return WechatWorkUser
     */
    protected function initAuthUserInfo(string $corpId, array $authUserInfo)
    {
        // 检查当前企业是否存在超级管理
        /** @var WechatWorkUser $adminUser */
        $adminUser = WechatWorkUser::withTrashed()->where('corp_id', $corpId)->where('is_admin', 1)->first();
        if ($adminUser) {
            if ($adminUser->deleted_at) {
                $adminUser->forceFill([
                    'deleted_at' => null,
                ])->save();
            }

            return $adminUser;
        }

        // 创建一个超级管理员
        return with(WechatWorkUser::query()->create([
            'corp_id' => $corpId,
            'userid' => $authUserInfo['userid'],
            'role_id' => 1,
            'is_admin' => 1,
        ]));
    }

    /**
     * 同步授权信息
     * @param array $authRawData
     * @return \App\Models\Wechat\WechatWorkCorp
     */
    public function syncOfQyAuthRawData($authRawData)
    {
        $permanentCode = $authRawData['permanent_code'];
        $corpInfo = $authRawData['auth_corp_info'];
        $corpId = $corpInfo['corpid'];

        // 同步企业信息
        $corp = $this->syncOfRawData($corpInfo);

        $agents = $authRawData['auth_info']['agent'];
        $agents = array_map(function (array $agent) use ($permanentCode) {
            $agent['permanent_code'] = $permanentCode;
            if (isset($agent['is_customized_app'])) {
                $agent['auth_type'] = $agent['is_customized_app']
                    ? WechatWorkAgent::AUTH_TYPE_CUSTOM_APP : WechatWorkAgent::AUTH_TYPE_SUITE;
            }

            return $agent;
        }, $agents);
        $newAgents = $this->syncAgentsOfRawAgents($corpId, $agents);

        $corp['agents'] = new Collection($newAgents);


        $authUserInfo = $authRawData['auth_user_info'];
        $registerInfo = $authRawData['register_code_info'] ?? [];

        if ($authUserInfo) {
            $corp['auth_user'] = $this->initAuthUserInfo($corpId, $authUserInfo);
        }

        return $corp;
    }

    /**
     * @param string $authCorpId
     * @param string $agentid
     */
    public function deleteAgentOnlyLocal($authCorpId, $agentid)
    {
        WechatWorkAgent::query()->where([
            'corp_id' => $authCorpId,
            'agentid' => $agentid,
        ])->delete();
    }

    /**
     * @return array|null
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function syncAgent($corpId, $agentId)
    {
        $data = $this->getAgentOfQy($corpId, $agentId);
        if (empty($data)) {
            return null;
        }

        return $this->syncAgentsOfRawAgents($corpId, [
            $data,
        ]);
    }

    /**
     * @return array|null
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function getAgentOfQy($corpId, $agentId)
    {
        $work = WeWork::workByCorpId($corpId, $agentId);
        $result = WechatResult::make($work->agent->get($agentId));

        return $result->isSucceeded() ? $result->toArray() : null;
    }
}
