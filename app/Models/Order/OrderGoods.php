<?php

namespace App\Models\Order;

use App\Models\Model;
use App\Models\WriteOff\WriteOffCode;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;

/**
 * @property-read int $id
 * @property-read string $corp_id
 * @property-read int $goods_num
 * @property-read string $activity_type
 * @property-read int $activity_id
 * @property-read int $order_id
 * @property-read int $user_id
 * @property-read Carbon $start_at
 * @property Carbon $overdue_at
 * @property-read string $goods_type
 * @property-read int $goods_id
 */
class OrderGoods extends Model
{
    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function activityable()
    {
        return $this->morphTo(__FUNCTION__, 'activity_type', 'activity_id')
            ->withoutGlobalScope(SoftDeletingScope::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function goodsable()
    {
        return $this->morphTo(__FUNCTION__, 'goods_type', 'goods_id')
            ->withoutGlobalScope(SoftDeletingScope::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function writeOffCodes()
    {
        return $this->hasMany(WriteOffCode::class, 'order_goods_id');
    }
}
