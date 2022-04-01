<?php

namespace App\Services\Wechat\Work;

use App\Models\Wechat\WechatWorkContactTag;
use App\Models\Wechat\WechatWorkContactTagGroup;
use App\Services\Wechat\WechatResult;
use Illuminate\Support\Carbon;

class ContactTagService extends WorkBaseService
{
    /**
     * @var string
     */
    protected $model = WechatWorkContactTag::class;

    /**
     * 同步数据到数据库中
     * @param array $data
     * @return WechatWorkContactTag
     */
    protected function syncTagData($data)
    {
        return WechatWorkContactTag::unguarded(function () use ($data) {
            $tagData = WechatWorkContactTag::getAllowFields($data);
            $tagData['tag_id'] = $data['id'];
            $tagData['corp_id'] = $this->corpId();
            $tagData['created_at'] = Carbon::createFromTimestamp($data['create_time']);
            if (isset($data['deleted'])) {
                $tagData['deleted_at'] = $data['deleted'] ? now() : null;
            }

            return WechatWorkContactTag::query()->updateOrCreate([
                'corp_id' => $tagData['corp_id'],
                'tag_id' => $tagData['tag_id'],
            ], $tagData);
        });
    }

    /**
     * 同步数据到数据库中
     * @param array $data
     * @return WechatWorkContactTagGroup
     */
    protected function syncGroupData($data)
    {
        return WechatWorkContactTagGroup::unguarded(function () use ($data) {
            $groupData = WechatWorkContactTagGroup::getAllowFields($data);
            $groupData['corp_id'] = $this->corpId();
            $groupData['created_at'] = Carbon::createFromTimestamp($data['create_time']);

            $group = WechatWorkContactTagGroup::query()->updateOrCreate([
                'corp_id' => $groupData['corp_id'],
                'group_id' => $groupData['group_id'],
            ], $groupData);

            $group->tags = array_map(function ($item) use ($data) {
                $item['group_id'] = $data['group_id'];

                return $this->syncTagData($item);
            }, $data['tag']);

            return $group;
        });
    }

    /**
     * 同步企微数据
     * @return WechatWorkContactTagGroup[]|array
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function syncAll()
    {
        $list = $this->getListOfQy();

        return array_map(function ($item) {
            return $this->syncGroupData($item);
        }, $list);
    }

    /**
     * 获取标签列表
     * @return array|null
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getListOfQy()
    {
        $result = WechatResult::make($this->work()->external_contact->getCorpTags());

        return $result->isSucceeded() ? $result['tag_group'] : null;
    }
}
