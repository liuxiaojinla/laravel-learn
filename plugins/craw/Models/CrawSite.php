<?php

namespace Plugins\craw\Models;

use App\Models\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * @property string $host
 * @property array $rules
 */
class CrawSite extends Model
{
    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var array
     */
    protected $casts = [
        'rules' => 'array',
    ];

    /**
     * @var string[]
     */
    protected static $excludeKeywords = null;

    /**
     * @var array
     */
    protected static $excludeHosts = null;

    /**
     * @inerhitDoc
     */
    protected static function boot()
    {
        parent::boot();

        static::loadExcludeKeywords();

        static::loadExcludeHosts();
    }

    /**
     * @return void
     */
    public static function loadExcludeKeywords()
    {
        if (self::$excludeKeywords !== null) {
            return;
        }

        static::refreshExcludeKeywords();
    }

    /**
     * @return void
     */
    public static function refreshExcludeKeywords()
    {
        self::$excludeKeywords = require __DIR__ . './exclude_keywords.php';
    }

    /**
     * @return void
     */
    public static function loadExcludeHosts()
    {
        if (self::$excludeHosts !== null) {
            return;
        }

        self::$excludeHosts = require __DIR__ . './exclude_hosts.php';
    }

    /**
     * @return void
     */
    public static function refreshLocalExcludeHosts()
    {
        self::$excludeHosts = require __DIR__ . './exclude_hosts.php';
    }

    /**
     * @return void
     */
    public static function refreshExcludeHosts()
    {
        static::refreshLocalExcludeHosts();

        $hosts = CrawSite::query()->where('deny', 1)->pluck('host')->toArray();
        self::$excludeHosts = array_unique(array_merge(self::$excludeHosts, $hosts));
    }


    /**
     * @return array
     */
    public static function getExcludeHosts()
    {
        static::loadExcludeHosts();

        return self::$excludeHosts;
    }

    /**
     * @param string $host
     * @return static
     */
    public static function put($host)
    {
        return DB::transaction(function () use ($host) {
            $site = static::query()->where('host', $host)->lockForUpdate()->first();
            if ($site) {
                $site->increment('view_count');
            } else {
                $site = static::query()->create([
                    'host' => $host,
                    'weight' => 999,
                ]);
            }

            return $site;
        });
    }

    /**
     * @return static
     */
    public static function popNotReadSite()
    {
        return DB::transaction(function () {
            /** @var static $first */
            $first = static::query()
                ->where('weight', '>', 0)
                ->where(function (Builder $query) {
                    $query->where('read_status', 0)
                        ->orWhere('read_at', '<', now()->subHour());
                })
                ->orderByDesc('weight')
                ->orderBy('id')->lockForUpdate()->first();

            if ($first) {
                $first->setReadStatus();
            }

            return $first;
        });
    }

    /**
     * @return void
     */
    public function setReadStatus()
    {
        $this->fill([
            'read_status' => 1,
            'read_at' => now(),
        ])->save();
    }


    /**
     * @param int $decrement
     * @param null $msg
     * @return void
     */
    public function reduceWeight($decrement = 1, $msg = '')
    {
        $weight = $this->getAttribute('weight') - $decrement;
        $this->decrement('weight', $decrement, [
            'msg' => $msg,
            'read_status' => 0,
            'deny' => $weight <= 0 ? 1 : 0,
        ]);
    }

    /**
     * @param int $decrement
     * @param string $title
     * @return void
     */
    public function raiseWeight($decrement = 1, $title = '')
    {
        $attributes = [];
        if (!empty($title)) {
            $attributes['title'] = $title;
        }
        $this->increment('weight', $decrement, $attributes);
    }

    /**
     * 设置为已禁用
     * @return bool
     */
    public function setDeny()
    {
        return $this->fill([
            'read_status' => 1,
            'deny' => 1,
        ])->save();
    }

    /**
     * 是否已禁用
     * @return bool
     */
    public function isDeny()
    {
        return $this->getAttribute('deny')
            || ($this->getAttribute('status') == 1 && $this->checkDenyWithTitle($this->getAttribute('title')));
    }

    /**
     * @param string $title
     * @return bool
     */
    public static function checkDenyWithTitle($title)
    {
        $optimizeTitle = str_replace(' ', '', $title);
        if (empty($title) || empty($optimizeTitle)) {
            return true;
        } elseif (Str::contains($title, static::$excludeKeywords) || Str::contains($optimizeTitle, static::$excludeKeywords)) {
            return true;
        } elseif (!static::checkEncoding($title)) {
            return true;
        }

        return false;
    }

    /**
     * @param string $value
     * @return bool
     */
    public static function checkEncoding($value)
    {
        return mb_detect_encoding($value, ['utf-8',], true);
    }

    /**
     * @param string $host
     * @return bool
     */
    public static function checkDenyWithHost($host)
    {
        if (empty($host)) {
            return true;
        }

        return Str::is(static::getExcludeHosts(), $host);
    }
}
