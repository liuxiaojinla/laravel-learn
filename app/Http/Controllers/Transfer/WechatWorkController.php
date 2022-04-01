<?php

namespace App\Http\Controllers\Transfer;

use App\Contracts\Wechat\Factory as WechatFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Ramsey\Uuid\Uuid;

class WechatWorkController
{
    /**
     * @var WechatFactory
     */
    private $wf;

    /**
     * @var array
     */
    private $whitelist = [
        'laravel8.test.com',
        'd9e5-61-52-227-54.ngrok.io',
    ];

    /**
     * @param WechatFactory $wf
     */
    public function __construct(WechatFactory $wf)
    {
        $this->wf = $wf;
    }

    public function go(Request $request)
    {
        $redirectUri = $request->input('redirect_uri');
        $redirectUri = urldecode($redirectUri);
        if (empty($redirectUri) || !$this->checkRedirectUri($redirectUri)) {
            return 'redirect_uri 不合法';
        }

        $authCode = $this->makeCode();

        Cache::put($this->makeRedirectCacheKey($authCode), $redirectUri, now()->addSeconds(7200));

        $callbackUrl = $request->fullUrl() . "/{$authCode}";

        return $this->wf->work()->oauth->redirect($callbackUrl);
    }

    /**
     * 业务跳转
     * @param Request $request
     * @param $authCode
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|string
     */
    public function redirect(Request $request, $authCode)
    {
        if (empty($authCode)) {
            return 'auth_code invalid.';
        }

        $callbackUrl = Cache::pull($this->makeRedirectCacheKey($authCode));
        if (empty($callbackUrl)) {
            return 'auth_code invalid.';
        }

        $callbackUrl = $callbackUrl . (strpos($callbackUrl, '?') ? '&' : '?') . 'code=' . $request->query('code') . '&state=' . $request->query('state');

        return redirect($callbackUrl);
    }

    /**
     * 生成一个回调地址缓存的key
     * @param string $authCode
     * @return string
     */
    private function makeRedirectCacheKey($authCode)
    {
        return 'auth:redirect:wechat_work:' . $authCode;
    }

    /**
     * @return string
     */
    private function makeCode()
    {
        return Uuid::uuid4()->toString();
    }

    /**
     * 校验回调地址是否合法
     * @param string $redirectUri
     * @return bool
     */
    private function checkRedirectUri($redirectUri)
    {
        $host = parse_url($redirectUri, PHP_URL_HOST);

        return in_array($host, $this->whitelist);
    }
}
