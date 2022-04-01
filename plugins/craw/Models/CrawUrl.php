<?php

namespace Plugins\craw\Models;

use App\Models\Model;
use Illuminate\Support\Facades\DB;
use Plugins\craw\Commands\OptimizeURL;

/**
 * @property string $url
 */
class CrawUrl extends Model
{
    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var array
     */
    protected static $excludeSuffix = [
        'jpg', 'bmp', 'jpeg', 'png', 'gif',
        'css', 'js',
    ];

    /**
     * @var array
     */
    protected static $sites = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function content()
    {
        return $this->hasOne(CrawContent::class);
    }

    /**
     * @return CrawSite
     */
    public function getSite()
    {
        $host = $this->getAttribute('host');
        if (!isset(self::$sites[$host])) {
            self::$sites[$host] = CrawSite::query()->where('host', $host)->first();
        }

        return self::$sites[$host];
    }

    /**
     * @return array
     */
    public function getSiteRules()
    {
        $site = $this->getSite();

        return $site ? ($site->rules ?: []) : [];
    }

    /**
     * @return bool
     */
    public function isAllowWriteContent()
    {
        $rules = $this->getSiteRules();

        return $this->build()->isPaths($rules);
    }

    /**
     * @param string $html
     * @return void
     */
    public function setContent($html, $title)
    {
        $id = $this->getAttribute('id');

        $this->raiseWeight(1, $title);

        CrawContent::query()->updateOrCreate([
            'craw_url_id' => $id,
        ], [
            'title' => $title,
            'url' => $this->getAttribute('url'),
            'host' => $this->getAttribute('host'),
            'path' => $this->getAttribute('path'),
            'content' => $html,
            'content_md5' => md5($html),
        ]);
    }

    /**
     * @return OptimizeURL|null
     */
    public function build($originUrl = null)
    {
        $url = $this->getAttribute('url');

        return OptimizeURL::parse($url, $originUrl ?: $url);
    }

    /**
     * @return bool
     */
    public function isActiveUrl($originUrl = null)
    {
        $url = $this->build($originUrl);

        return $url && static::checkActiveUrl($url);
    }

    /**
     * @return void
     */
    public function setReadStatus($msg = null)
    {
        $data = [
            'read_link_status' => 1,
            'read_link_at' => now(),
        ];

        if ($msg) {
            $data['msg'] = $msg;
        }

        $this->fill($data)->save();
    }

    /**
     * @return void
     */
    public function setUnReadStatus($msg, $priority = 0)
    {
        $data = [
            'read_link_status' => 0,
            'msg' => $msg,
        ];

        if ($priority) {
            $data['priority'] = $priority;
        }

        $this->fill($data)->save();
    }

    /**
     * @param int $decrement
     * @param null $msg
     * @return false|int
     */
    public function reduceWeight($decrement = 1, $msg = '', $extra = [])
    {
        return $this->decrement('priority', $decrement, array_merge($extra, [
            'msg' => $msg,
        ]));
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
        $this->increment('priority', $decrement, $attributes);
    }

    /**
     * @return static
     */
    public static function pop()
    {
        return DB::transaction(function () {
            /** @var static $first */
            $first = static::query()->where('read_link_status', 0)->orderByDesc('priority')->orderBy('id')->lockForUpdate()->first();

            if ($first) {
                $first->setReadStatus();
            }

            return $first;
        });
    }

    /**
     * @param OptimizeURL $url
     * @return bool
     */
    public static function checkActiveUrl(OptimizeURL $url)
    {
        if ($url->isHosts(CrawSite::getExcludeHosts())) {
            return false;
        }

        if ($url->getExtension() && $url->isExtensions(static::$excludeSuffix)) {
            return false;
        }

        return true;
    }
}
