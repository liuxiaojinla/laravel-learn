<?php

namespace App\Services\Wechat\Work;

use App\Models\Wechat\WechatWorkContactWay;
use App\Services\Wechat\WechatResult;

class ContactWayService extends WorkBaseService
{
    /**
     * @var string
     */
    protected $model = WechatWorkContactWay::class;

    /**
     * 同步数据到数据库中
     * @param array $data
     * @return WechatWorkContactWay
     */
    public function syncOfRawData($data)
    {
        return WechatWorkContactWay::unguarded(function () use ($data) {
            $data = WechatWorkContactWay::getAllowFields($data);
            $data['corp_id'] = $this->corpId();

            return $this->dbQuery()->updateOrCreate([
                'config_id' => $data['config_id'],
            ], $data);
        });
    }

    /**
     * 同步单个数据
     * @param string $configId
     * @return WechatWorkContactWay|null
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sync($configId)
    {
        $result = $this->getOfQy($configId);
        if (empty($result)) {
            return null;
        }

        return $this->syncOfRawData($result);
    }

    /**
     * 同步所有数据
     * @return bool
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function syncAll()
    {
        $cursor = null;
        while (true) {
            $result = $this->getListOfQy($cursor);
            if ($result === null) {
                return false;
            }

            $list = $result['contact_way'] ?? [];
            foreach ($list as $item) {
                $this->sync($item['config_id']);
            }

            $cursor = $result['next_cursor'] ?? null;
            if (empty($cursor)) {
                break;
            }
        }

        return true;
    }

    /**
     * 获取企业已配置的「联系我」方式（企微）
     * @param string $configId
     * @return array|null
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getOfQy($configId)
    {
        $result = WechatResult::make($this->work()->contact_way->get($configId));

        return $result->isSucceeded() ? $result['contact_way'] : null;
    }

    /**
     * 获取企业已配置的「联系我」列表（企微）
     * @param string $cursor
     * @return array|null
     */
    public function getListOfQy($cursor = null)
    {
        $result = WechatResult::make(
            $this->work()->contact_way->list([
                'cursor' => $cursor,
            ])
        );

        return $result->isSucceeded() ? $result->toArray() : null;
    }
}
