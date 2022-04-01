<?php

namespace App\Services\Wechat;

use App\Contracts\Wechat\Factory as WechatFactory;

class BaseService
{
    /**
     * @var WechatFactory
     */
    private $wechatFactory;

    /**
     * @var string
     */
    private $syncBatchNo;

    /**
     * @var string
     */
    protected $model;

    /**
     * @var string
     */
    protected $appId;

    /**
     * @param WechatFactory $wechatFactory
     */
    public function __construct(WechatFactory $wechatFactory)
    {
        $this->wechatFactory = $wechatFactory;
    }

    /**
     * @return WechatFactory
     */
    public function getWechatFactory()
    {
        return $this->wechatFactory;
    }

    /**
     * @return \EasyWeChat\Work\Application
     */
    protected function work()
    {
        return $this->wechatFactory->work();
    }

    /**
     * @return \EasyWeChat\OfficialAccount\Application
     */
    protected function official()
    {
        return $this->wechatFactory->official();
    }

    /**
     * @return string
     */
    protected function appId()
    {
        if (!$this->appId) {
            $this->appId = $this->work()->config['app_id'];
        }

        return $this->appId;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function dbQuery()
    {
        return $this->model::query()->where('corp_id', $this->corpId());
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function dbWithTrashedQuery()
    {
        return $this->model::withTrashed()->where('corp_id', $this->corpId());
    }

    /**
     * 生成批次号
     * @return string
     */
    protected function generateBatchNo()
    {
        return now()->format('YmdHis');
    }

    /**
     * 开始批次处理
     */
    public function startBatch($syncBatchNo = null)
    {
        return $this->syncBatchNo = $syncBatchNo ?: $this->generateBatchNo();
    }

    /**
     * 结束批次处理
     * @param callable|null $callback
     */
    public function completeBatch(callable $callback = null)
    {
        $batchNo = $this->syncBatchNo;
        $this->syncBatchNo = null;

        if ($batchNo) {
            call_user_func($callback, $batchNo);
        }
    }

    /**
     * 获取批次编号
     * @return string
     */
    public function getSyncBatchNo()
    {
        return $this->syncBatchNo;
    }
}
