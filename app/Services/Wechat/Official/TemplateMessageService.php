<?php

namespace App\Services\Wechat\Official;

use App\Models\Wechat\Officials\TemplateMessage;
use App\Services\Wechat\BaseService;
use App\Services\Wechat\WechatResult;
use Illuminate\Database\Eloquent\Collection;

class TemplateMessageService extends BaseService
{
    /**
     * @var string
     */
    protected $model = TemplateMessage::class;

    /**
     * 删除模板
     * @param string $templateId
     * @return false
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deleteByTemplateId($templateId)
    {
        if (!$this->deletePrivateOfWechat($templateId)) {
            return false;
        }

        return $this->deleteByTemplateIdOnlyLocal($templateId);
    }

    /**
     * 仅删除本地数据
     * @param string $templateId
     * @return bool
     */
    public function deleteByTemplateIdOnlyLocal($templateId)
    {
        $this->dbQuery()->where('app_id', $this->appId())->where('template_id', $templateId)->delete();

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
                'template_id' => $data['template_id'],
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

        $privateTemplates = $this->getPrivateListOfWechat();

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
        return WechatResult::make($this->official()->template_message->send($data));
    }

    /**
     * 发送模板消息订阅通知（微信）
     * @param array $data
     * @return WechatResult
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendSubscription($data)
    {
        return WechatResult::make($this->official()->template_message->sendSubscription($data));
    }

    /**
     * 获取私有模板库列表（微信）
     * @return array|null
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPrivateListOfWechat()
    {
        $result = WechatResult::make($this->official()->template_message->getPrivateTemplates());

        return $result->isSucceeded() ? $result['template_list'] : null;
    }

    /**
     * 删除模板（微信）
     * @param string $templateId
     * @return bool
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deletePrivateOfWechat($templateId)
    {
        $result = WechatResult::make($this->official()->template_message->deletePrivateTemplate($templateId));

        return $result->isSucceeded();
    }
}
