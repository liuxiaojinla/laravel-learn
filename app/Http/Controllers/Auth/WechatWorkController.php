<?php

namespace App\Http\Controllers\Auth;

use App\Contracts\Wechat\Factory as WechatFactory;
use App\Support\Facades\Hint;
use Illuminate\Http\Request;

class WechatWorkController
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function __invoke(Request $request)
    {
        if ($request->has('code')) {
            return $this->login($request);
        } else {
            return $this->authorize($request);
        }
    }

    /**
     * 构建授权链接，并进行跳转
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    private function authorize(Request $request)
    {
        $authUrl = $request->root() . '/auth/transfer/wechat_work?redirect_uri=' . urlencode($request->url());

        return redirect($authUrl);
    }

    /**
     * 登录
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    private function login(Request $request)
    {
        $wf = app(WechatFactory::class);
        $user = $wf->work()->oauth->detailed()->user();
        if (!$user->getId()) {
            return Hint::result($user);
        }

        return Hint::result($user);
    }
}
