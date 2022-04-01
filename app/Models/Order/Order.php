<?php

namespace App\Models\Order;

use App\Models\Model;
use App\Models\WriteOff\WriteOffCode;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use JetBrains\PhpStorm\Pure;

/**
 * @property int $user_id
 * @property string $order_no
 * @property float $total_price
 * @property float $pay_price
 * @property Collection $orderGoodsList
 * @property \Illuminate\Database\Eloquent\Model $activityable
 * @property string $statusText
 * @property int $status
 */
class Order extends Model
{
    // 等待中
    const STATUS_WAITING = 1;

    // 已使用
    const STATUS_USED = 2;

    // 已过期
    const STATUS_OVERDUE = 3;

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var string[]
     */
    protected $with = [
        'orderGoodsList',
        'activityable',
        'orderGoodsList.goodsable',
        'writeOffCodes',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderGoodsList()
    {
        return $this->hasMany(OrderGoods::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function activityable()
    {
        return $this->morphTo(__FUNCTION__, 'activity_type', 'activity_id')
            ->withoutGlobalScope(SoftDeletingScope::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function writeOffCodes()
    {
        return $this->hasMany(WriteOffCode::class, 'order_id');
    }

    /**
     * 是否待使用
     * @return bool
     */
    public function isWaiting()
    {
        return $this->getAttribute('status') == static::STATUS_WAITING;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function statusText(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                return match ($this->getAttribute('status')) {
                    static::STATUS_WAITING => '待使用',
                    static::STATUS_USED => '已使用',
                    static::STATUS_OVERDUE => '已过期',
                    default => '未知'
                };
            },
        );
    }

    /**
     * 状态搜索器
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $value
     * @return void
     */
    public function searchStatusAttribute($query, $value)
    {
        $value = (int) $value;
        if ($value == 0) {
            return;
        }

        $query->where('status', $value);
    }

    /**
     * @return array
     */
    #[Pure]
    public static function getSearchFields(): array
    {
        return array_merge(parent::getSearchFields(), [
            'status', 'order_no',
        ]);
    }
}
