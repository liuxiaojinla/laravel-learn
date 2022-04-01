<?php

namespace App\Services\Wechat\Official;

use App\Models\Wechat\Officials\SubscriptionMessage;
use App\Models\Wechat\Officials\TemplateMessage;
use App\Services\Wechat\BaseService;
use App\Services\Wechat\WechatResult;
use Illuminate\Database\Eloquent\Collection;

class SubscriptionMessageService extends BaseService
{
    /**
     * @var string
     */
    protected $model = SubscriptionMessage::class;

    /**
     * 删除模板
     * @param string $priTmplId
     * @return false
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deleteByPriTmplId($priTmplId)
    {
        if (!$this->deleteOfWechat($priTmplId)) {
            return false;
        }

        return $this->deleteByTemplateIdOnlyLocal($priTmplId);
    }

    /**
     * 仅删除本地数据
     * @param string $priTmplId
     * @return bool
     */
    public function deleteByTemplateIdOnlyLocal($priTmplId)
    {
        $this->dbQuery()->where('app_id', $this->appId())->where('pri_tmpl_id', $priTmplId)->delete();

        return true;
    }

    /**
     * 同步数据到数据库中
     * @param array $data
     * @return TemplateMessage
     */
    public function syncOfRawData($data)
    {
        return TemplateMessage::unguarded(function () use ($data) {
            $data = TemplateMessage::getAllowFields($data);
            $data['app_id'] = $this->appId();
            $data['deleted_at'] = null;

            $syncBatchNo = $this->getSyncBatchNo();
            if ($syncBatchNo) {
                $data['sync_batch_no'] = $syncBatchNo;
            }

            return $this->dbWithTrashedQuery()->updateOrCreate([
                'pri_tmpl_id' => $data['pri_tmpl_id'],
            ], $data);
        });
    }

    /**
     * 同步一组数据到数据库中
     * @param array $items
     * @return Collection|\Illuminate\Support\Collection
     */
    public function syncOfRawItems($items)
    {
        return (new Collection($items))->map(function ($data) {
            return $this->syncOfRawData($data);
        });
    }

    /**
     * 同步所有数据
     * @return Collection|\Illuminate\Support\Collection
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function syncAll()
    {
        $this->startBatch();

        $privateTemplates = $this->getListOfWechat();

        $privateTemplates = $this->syncOfRawItems($privateTemplates);

        $this->completeBatch(function ($batchNo) {
            $this->dbQuery()->where('sync_batch_no', '<>', $batchNo)->delete();
        });

        return $privateTemplates;
    }

    /**
     * 发送模板消息（微信）
     * @param array $data
     * @return WechatResult
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function send($data)
    {
        return WechatResult::make($this->official()->subscribe_message->send($data));
    }

    /**
     * 获取公众号类目（微信）
     * @return array|null
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getCategoryOfWechat()
    {
        $result = WechatResult::make($this->official()->subscribe_message->getCategory());

        return $result->isSucceeded() ? $result['data'] : null;
    }

    /**
     * 获取模板中的关键词（微信）
     * @param string $tid
     * @return array|null
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPubTemplateKeyWordsByIdOfWechat($tid)
    {
        $result = WechatResult::make($this->official()->subscribe_message->getTemplateKeywords($tid));

        return $result->isSucceeded() ? $result->toArray() : null;
    }

    /**
     * 获取类目下的公共模板（微信）
     * @param array $ids
     * @param int $start
     * @param int $limit
     * @return array|null
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPubTemplateTitleListOfWechat($ids, $start = 0, $limit = 30)
    {
        $result = WechatResult::make($this->official()->subscribe_message->getTemplateTitles($ids, $start, $limit));

        return $result->isSucceeded() ? $result->toArray() : null;
    }

    /**
     * 获取私有模板库列表（微信）
     * @return array|null
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getListOfWechat()
    {
        $result = WechatResult::make($this->official()->subscribe_message->getTemplates());

        return $result->isSucceeded() ? $result['data'] : null;
    }

    /**
     * 删除模板（微信）
     * @param string $templateId
     * @return bool
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deleteOfWechat($templateId)
    {
        $result = WechatResult::make($this->official()->subscribe_message->deleteTemplate($templateId));

        return $result->isSucceeded();
    }
}
