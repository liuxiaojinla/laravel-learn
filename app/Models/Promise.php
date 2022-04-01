<?php

namespace App\Models;

use App\Contracts\Promise\Promise as PromiseContract;
use App\Models\Wechat\WechatWorkUser;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

/**
 * @property-read int id
 * @property-read string $corp_id
 * @property-read int parent_id
 * @property-read int oper_id
 * @property-read string taskable_type
 * @property-read int taskable_id
 * @property-read int status
 * @property-read Carbon begin_at
 * @property-read Carbon completed_at
 * @property-read Carbon stopped_at
 * @property string error
 * @property array user_data
 * @property-read int $expect_quantity
 * @property \Illuminate\Support\Carbon $created_at
 */
class Promise extends Model implements PromiseContract
{
    use SoftDeletes;

    // 等待中
    const STATUS_WAITING = 1;

    // 进行中
    const STATUS_PENDING = 2;

    // 已完成
    const STATUS_COMPLETED = 3;

    // 已停止（用户手动停止）
    const STATUS_STOPPED = 4;

    /**
     * @var array
     */
    protected static $STATUS_TEXT = [
        self::STATUS_WAITING => '等待中',
        self::STATUS_PENDING => '进行中',
        self::STATUS_COMPLETED => '已结束',
        self::STATUS_STOPPED => '已停止',
    ];

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var array
     */
    protected $casts = [
        'filter' => 'array',
    ];

    /**
     * @var string[]
     */
    protected $dates = [
        'begin_at',
        'stopped_at',
        'completed_at',
    ];

    /**
     * @return int
     */
    public function getId()
    {
        return $this->getAttribute('id');
    }

    /**
     * 当前任务是否等待中（前端展示应统一展示为进行中）
     * @return bool
     */
    public function isWaiting()
    {
        return $this->isStatus(self::STATUS_WAITING);
    }

    /**
     * 当前任务是否为进行中（前端展示应统一展示为进行中）
     * @return bool
     */
    public function isPending()
    {
        return $this->isStatus(self::STATUS_PENDING);
    }

    /**
     * 当前任务是否已结束（包含已失败的）
     * @return bool
     */
    public function isCompleted()
    {
        return $this->isStatus(self::STATUS_COMPLETED);
    }

    /**
     * 是否已成功
     * @return bool
     */
    public function isSucceeded()
    {
        return $this->isCompleted() && $this->getAttribute('error') == null;
    }

    /**
     * 是否已失败
     * @return bool
     */
    public function isFailed()
    {
        return $this->isCompleted() && $this->getAttribute('error') != null;
    }

    /**
     * 当前任务是否已手动停止（用户是否已手动停止）
     * @return bool
     */
    public function isStopped()
    {
        return $this->isStatus(self::STATUS_STOPPED);
    }

    /**
     * 当前任务是否已过期
     * @return bool
     */
    public function isExpired()
    {
        return $this->isSucceeded() && $this->completed_at->lt(now()->subDays(30));
    }

    /**
     * 当前任务状态
     * @param int $status
     * @return bool
     */
    public function isStatus($status)
    {
        return $this->getAttribute('status') == $status;
    }

    /**
     * 设置为等待状态
     * @param array $attributes
     * @return bool
     * @deprecated 不建议使用，如果任务需要重试，建议新创建一条记录
     */
    public function setWaitingStatus($attributes = [])
    {
        $attributes['error'] = null;

        return $this->setStatus(
            self::STATUS_WAITING,
            $attributes
        );
    }

    /**
     * 设置任务为进行中的状态
     * @param array $attributes
     * @return bool
     */
    public function setPendingStatus($attributes = [])
    {
        $attributes['error'] = null;
        $attributes['begin_at'] = now();

        return $this->setStatus(
            self::STATUS_PENDING,
            $attributes
        );
    }

    /**
     * 设置失败的状态
     * @param string $error
     * @param array $attributes
     * @return bool
     */
    public function setFailedStatus($error, $attributes = [])
    {
        return $this->setCompletedStatus($error, $attributes);
    }

    /**
     * 设置成功的状态
     * @param array $attributes
     * @return bool
     */
    public function setSucceededStatus($attributes = [])
    {
        return tap($this->setCompletedStatus(null, $attributes), function () {
            $this->setProgress(100);
        });
    }

    /**
     * 设置任务为已结束状态
     * @param string $error
     * @param array $attributes
     * @return bool
     */
    protected function setCompletedStatus($error = null, $attributes = [])
    {
        $attributes['error'] = $error;
        $attributes['completed_at'] = now();

        return $this->setStatus(
            self::STATUS_COMPLETED,
            $attributes
        );
    }

    /**
     * 设置任务为已结束状态
     * @param string $error
     * @param array $attributes
     * @return bool
     */
    public function setStoppedStatus($error = null, $attributes = [])
    {
        $attributes['error'] = $error;
        $attributes['stopped_at'] = now();

        return $this->setStatus(
            self::STATUS_COMPLETED,
            $attributes
        );
    }

    /**
     * 设置状态
     * @param int $status
     * @param array $attributes
     * @return bool
     */
    protected function setStatus($status, $attributes = [])
    {
        $attributes['status'] = $status;

        return $this->fill($attributes)->save();
    }

    /**
     * 实例化新任务（等待状态）
     * @param array $attributes
     * @return $this
     */
    public function withWaitingStatus($attributes = [])
    {
        return $this->withStatus(self::STATUS_WAITING, $attributes);
    }

    /**
     * 实例化新任务（进行中状态）
     * @param array $attributes
     * @return $this
     */
    public function withPendingStatus($attributes = [])
    {
        return $this->withStatus(self::STATUS_PENDING, $attributes);
    }

    /**
     * 实例化新任务
     * @param int $status
     * @param array $attributes
     * @return $this
     */
    protected function withStatus($status, $attributes = [])
    {
        $attributes['parent_id'] = $this->getId();
        $attributes['status'] = $status;

        return static::make($attributes);
    }

    /**
     * @return string
     */
    protected function getStatusTextAttribute()
    {
        $status = $this->getAttribute('status');

        return self::$STATUS_TEXT[$status] ?? '-';
    }

    /**
     * 获取进度条进度
     * @return float
     */
    protected function getProgressAttribute()
    {
        return $this->getProgress();
    }

    /**
     * 获取进度条进度
     * @return float
     */
    public function getProgress()
    {
        $id = $this->getOriginal('id');

        return Cache::get(
            static::progressCacheKey($id),
            '0.00'
        );
    }

    /**
     * 设置进度条
     * @param float $progress
     */
    public function setProgress($progress)
    {
        $id = $this->getOriginal('id');
        $cacheKey = static::progressCacheKey($id);
        Cache::put($cacheKey, $progress, now()->addDays());
    }

    /**
     * 根据已执行的数量设置进度
     * @param int $count
     */
    public function setProgressByCount($count)
    {
        $progress = 0;
        if ($this->expect_quantity) {
            $progress = bcmul(bcdiv($count, $this->expect_quantity, 4), 100, 2);
            if ($progress > 100) {
                $progress = '100.00';
            }
        }

        $this->setProgress($progress);

        return $progress;
    }

    /**
     * 获取进度条进度
     * @return float
     */
    public function getProgressCount()
    {
        $id = $this->getOriginal('id');
        $cacheKey = static::progressCacheKey($id) . ':count';

        return Cache::get($cacheKey, 0);
    }

    /**
     * 根据已执行的数量设置进度（自增）
     * @param string $count
     * @return string
     */
    public function setProgressByIncCount($count)
    {
        $id = $this->getOriginal('id');
        $cacheKey = static::progressCacheKey($id) . ':count';
        $current = Cache::increment($cacheKey, $count);

        return $this->setProgressByCount($current);
    }

    /**
     * 获取进度条进度
     * @param int $id
     * @return string
     */
    protected static function progressCacheKey($id)
    {
        return "promise:progress:{$id}";
    }

    /**
     * 异步执行命令
     * @return \Illuminate\Foundation\Bus\PendingDispatch
     */
    public function dispatch($jobClass)
    {
        if (app()->environment('test')) {
            return $jobClass::dispatchNow($this);
        }

        return $jobClass::dispatch($this);
    }

    /**
     * 同步执行命令
     * @return mixed
     */
    public function dispatchNow($jobClass)
    {
        return $jobClass::dispatchNow($this);
    }

    /**
     * 关联员工表
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function oper()
    {
        return $this->belongsTo(WechatWorkUser::class);
    }

    /**
     * 创建任务
     * @param array $attributes
     * @return static
     */
    public static function make($taskType, $taskableId = 0, $attributes = [])
    {
        $attributes = static::newAttributes($attributes);
        $attributes['taskable_type'] = $taskType;
        $attributes['taskable_id'] = $taskableId;

        /** @var static $instance */
        return with(static::query()->create($attributes));
    }

    /**
     * @param array $attributes
     * @return array|int[]|string[]
     */
    public static function newAttributes($attributes = [])
    {
        return array_merge([
            'status' => self::STATUS_WAITING,
            'parent_id' => 0,
            'expect_quantity' => 0,
            'actual_quantity' => 0,
            'error_rows' => 0,
            'user_data' => null,
            'error' => null,
            'begin_at' => null,
            'completed_at' => null,
            'stopped_at' => null,
        ], $attributes);
    }
}
