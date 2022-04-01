<?php

namespace Plugins\craw\Commands;

use Illuminate\Support\Str;

class OptimizeURL
{
    /**
     * @var array
     */
    protected static $excludeKeywords = [
        'javascript:',
        'window.',
        'history.',
        'void(0)',
    ];

    /**
     * @var array
     */
    protected $info;

    /**
     * @param array $info
     */
    protected function __construct(array $info)
    {
        $this->info = $info;
    }

    /**
     * @return string
     */
    public function host()
    {
        return $this->info['host'];
    }

    /**
     * @param string $target
     * @return bool
     */
    public function isHost($target)
    {
        return Str::is($target, $this->info['host']);
    }

    /**
     * @param array $hosts
     * @return bool
     */
    public function isHosts(array $hosts)
    {
        foreach ($hosts as $host) {
            if ($this->isHost($host)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return string
     */
    public function path()
    {
        return $this->info['path'];
    }

    /**
     * @param string $pattern
     * @return bool
     */
    public function isPath($pattern)
    {
        return Str::is($pattern, $this->path());
    }

    /**
     * @param array $pattern
     * @return bool
     */
    public function isPaths(array $pattern)
    {
        return Str::is($pattern, $this->path());
    }

    /**
     * 获取后缀类型
     * @return string
     */
    public function getExtension()
    {
        return strtolower(substr(strrchr($this->info['path'], '.'), 1));
    }

    /**
     * @param array $suffixList
     * @return bool
     */
    public function isExtensions(array $suffixList)
    {
        return in_array($this->getExtension(), $suffixList);
    }

    /**
     * @return string
     */
    public function build()
    {
        $path = $this->info['path'] ?? '';

        return ($this->info['scheme'] ? $this->info['scheme'] . '://' : '//')
            . $this->info['host']
            . (strpos($path, '/') === 0 ? '' : '/') . $path
            . (isset($this->info['query']) ? '?' . $this->info['query'] : '');
    }

    /**
     * @return string
     */
    public function md5()
    {
        return md5($this->build());
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->build();
    }

    /**
     * @param string $url
     * @return bool
     */
    public static function isExclude($url)
    {
        $cleanUrl = trim($url);

        foreach (static::$excludeKeywords as $keyword) {
            if (stripos($cleanUrl, $keyword) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $url
     * @return string
     */
    public static function optimize($url)
    {
        if (strpos($url, 'http://') !== 0 && strpos($url, 'https://') !== 0 && strpos($url, '//') !== 0) {
            $url = '//' . $url;
        }

        return $url;
    }

    /**
     * @param string $url
     * @param string $originUrl
     * @return static|null
     */
    public static function parse($url, $originUrl)
    {
        if (static::isExclude($url)) {
            return null;
        }

        $url = self::optimize($url);

        $urlInfo = parse_url($url);
        if (!$urlInfo) {
            return null;
        }

        $urlInfo = array_merge([
            'scheme' => '',
            'host' => '',
            'path' => '',
        ], $urlInfo);

        if (empty($urlInfo['host'])) {
            $originUrlInfo = parse_url($originUrl);
            if (!$originUrlInfo) {
                return null;
            }

            if (isset($originUrlInfo['scheme'])) {
                $urlInfo['scheme'] = $originUrlInfo['scheme'] ?? '';
            }

            if (isset($originUrlInfo['host'])) {
                $urlInfo['host'] = $originUrlInfo['host'];
            }
        }

        if ($urlInfo['scheme'] && !in_array($urlInfo['scheme'], ['http', 'https'])) {
            return null;
        }

        return new static($urlInfo);
    }


}
