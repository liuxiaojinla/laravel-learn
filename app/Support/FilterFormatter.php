<?php

namespace App\Support;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

/**
 * @property-read string corpId
 * @property string search
 * @property string searchType
 * @property Carbon startTime
 * @property Carbon endTime
 * @property string type
 * @property int today
 * @property int yesterday
 * @property string $sort
 * @property string $sortType
 * @property int $status
 */
class FilterFormatter extends Formatter
{
    /**
     * @var string[]
     */
    protected $type = [
        'corpId' => 'string',
        'search' => 'string',
        'searchType' => 'string',
        'startTime' => 'datetime',
        'endTime' => 'datetime',
        'type' => 'string',
        'today' => 'int',
        'yesterday' => 'int',
        'sort' => 'string',
        'sortType' => 'string',
    ];

    /**
     * @var array
     */
    protected $attributes = [
        'sortType' => 'DESC',
    ];

    /**
     * @var array
     */
    protected $allowSearchTypes;

    /**
     * @var array
     */
    protected $allowSorts = [];

    /**
     * @var int
     */
    protected $maxRangeTimeDay = 180;

    /**
     * 设置搜索类型
     * @param array $types
     * @return $this
     */
    public function setAllowSearchTypes(array $types)
    {
        $this->allowSearchTypes = $types;

        return $this;
    }

    /**
     * 设置运行的排序类型
     * @param array $sorts
     * @return $this
     */
    public function setAllowSorts(array $sorts)
    {
        $this->allowSorts = $sorts;

        return $this;
    }

    /**
     * 设置最大时间区间（单位：天）
     * @param int $rangeDay
     * @return $this
     */
    public function setMaxRangeTimeDay($rangeDay)
    {
        $this->maxRangeTimeDay = $rangeDay;

        return $this;
    }

    /**
     * 清除区间时间
     * @return $this
     */
    public function clearRangeTime()
    {
        $this->attributes['startTime'] = null;
        $this->attributes['endTime'] = null;
        $this->today = 0;
        $this->yesterday = 0;

        return $this;
    }

    /**
     * 锁定昨天时间区间
     * @param int $lock
     * @return $this
     */
    public function lockYesterdayRangeTime($lock = 1)
    {
        $this->yesterday = $lock;

        return $this;
    }

    /**
     * 锁定今天时间区间
     * @param int $lock
     * @return $this
     */
    public function lockTodayRangeTime($lock = 1)
    {
        $this->today = $lock;

        return $this;
    }

    /**
     * 设置之前到现在的区间
     * @param int $day
     * @return $this
     */
    public function setAgoDays($day = 1)
    {
        $this->startTime = now()->startOfDay()->subDays($day);
        $this->endTime = now()->startOfDay();

        return $this;
    }

    /**
     * 设置最近7天时间区间
     * @return $this
     */
    public function setAgoDaysOf7()
    {
        return $this->setAgoDays(7);
    }

    /**
     * 设置最近30天时间区间
     * @return $this
     */
    public function setAgoDaysOf30()
    {
        return $this->setAgoDays(30);
    }

    /**
     * 设置最近90天时间区间
     * @return $this
     */
    public function setAgoDaysOf90()
    {
        return $this->setAgoDays(90);
    }

    /**
     * 设置月时间区间
     * @return $this
     */
    public function setMonthRangeTime($date = null)
    {
        if ($date) {
            $startTime = Carbon::parse($date)->startOfMonth();
        } else {
            $startTime = now()->startOfMonth();
        }

        $endTime = $startTime->copy()->endOfMonth();
        if ($endTime->gt(now())) {
            $endTime = now()->subDays()->endOfDay();
        }

        $this->startTime = $startTime;
        $this->endTime = $endTime;

        return $this;
    }

    /**
     * 获取范围时间
     * @return Carbon[]
     */
    public function getRangeTime()
    {
        if ($this->today) {
            $startTime = now()->startOfDay();
            $endTime = now()->endOfDay();
        } elseif ($this->yesterday) {
            $startTime = now()->subDays()->startOfDay();
            $endTime = now()->subDays()->endOfDay();
        } else {
            $startTime = $this->startTime ?? now()->startOfDay()->subDays(8);
            $endTime = $this->endTime ?? now()->endOfDay()->subDays();
        }

        if ($startTime->gt($endTime)) {
            return [$endTime, $startTime];
        }

        // 如果时间超出最大区间，则以起始时间 + 最大区间数量
        if ($startTime->diffInDays($endTime) > $this->maxRangeTimeDay) {
            $endTime = $startTime->copy()->addDays($this->maxRangeTimeDay);
        }

        return [$startTime, $endTime];
    }

    /**
     * 是否单日期查询
     * @return bool
     */
    public function isSimpleDate()
    {
        return $this->today || $this->yesterday;
    }

    /**
     * 是否存在区间时间查询
     * @return bool
     */
    public function hasRangeTime()
    {
        return ($this->startTime || $this->endTime) || $this->isSimpleDate();
    }

    /**
     * 日期查询处理
     * @param Builder|\Illuminate\Database\Query\Builder|Model $builder
     * @return Builder|mixed
     */
    public function buildDateQuery($builder, $timeField = 'created_at', $format = null)
    {
        return $builder->when($this->hasRangeTime(), function ($builder) use ($timeField, $format) {
            $rangeTime = $this->getRangeTime();

            /** @var Builder|\Illuminate\Database\Query\Builder|Model $builder */
            if ($format) {
                $builder->whereBetween($timeField, [
                    $rangeTime[0]->format($format), $rangeTime[1]->format($format),
                ]);
            } else {
                $builder->whereBetween($timeField, $rangeTime);
            }
        });
    }

    /**
     * @inheritDoc
     */
    protected function writeDatetimeTransform($value)
    {
        if ($value instanceof Carbon) {
            return $value;
        }

        if (is_numeric($value)) {
            return \Illuminate\Support\Carbon::createFromTimestamp($value);
        }

        return Carbon::parse($value);
    }

    /**
     * 设置搜索字符串
     * @param string $value
     * @return string
     */
    protected function setSearchAttribute($value)
    {
        return mb_substr($value, 0, 48);
    }

    /**
     * 设置搜索字符串类型
     * @param string $value
     * @return string
     * @throws ValidationException
     */
    protected function setSearchTypeAttribute($value)
    {
        if (!in_array($value, $this->allowSearchTypes)) {
            static::throwInvalidParameterException('search_type');
        }

        return $value;
    }

    /**
     * 设置排序字段
     * @param string $value
     * @return string
     * @throws ValidationException
     */
    protected function setSortAttribute($value)
    {
        if (!in_array($value, $this->allowSorts)) {
            static::throwInvalidParameterException('sort');
        }

        return $value;
    }

    /**
     * 抛出参数无效异常
     * @param string $parameter
     * @throws ValidationException
     */
    protected static function throwInvalidParameterException($parameter)
    {
        throw ValidationException::withMessages([
            'default' => [
                "{$parameter} is invalid.",
            ],
        ]);
    }
}
