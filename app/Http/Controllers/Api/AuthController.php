<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ValidationException;
use App\Models\WechatUser;
use App\Services\Wechat\WechatResult;
use App\Support\WeWork;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\ValidationException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function login(Request $request)
    {
        $deviceName = $request->input('device_name', 'web');
        $code = (string) $request->input('code');

        $user = null;
        $sessionKey = null;

        if ($code) {
            $user = $this->loginByMiniProgramCode($code, $sessionKey);
        }

        // 本地开发模式兼容
        if (app()->environment('local') && !$code) {
            $user = $this->localDevModeHandle($request);
        }

        $accessToken = $user->createToken($deviceName);

        return success(array_merge([
            'user' => $user,
            'session_key' => $sessionKey,
        ], $accessToken->toArray()));
    }

    /**
     * 小程序登录
     * @param string $code
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     * @throws \App\Exceptions\ValidationException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    protected function loginByMiniProgramCode($code, &$sessionKey = null)
    {
        $authInfo = $this->getWechatAuthInfo($code);
        $sessionKey = $authInfo['session_key'];

        return WechatUser::unguarded(function () use ($authInfo) {
            /** @var WechatUser $user */
            return WechatUser::query()->updateOrCreate([
                'openid' => $authInfo['openid'],
            ]);
        });
    }

    /**
     * 获取微信授权信息
     * @param string $code
     * @return array
     * @throws \App\Exceptions\ValidationException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    protected function getWechatAuthInfo(string $code)
    {
        $result = WechatResult::make(WeWork::miniprogram()->auth->session($code));
        if (!$result->isSucceeded()) {
            Log::error('C端登录失败：', [
                $code, $result->getRaw(),
            ]);
            //todo 优化提示
            throw ValidationException::withMessage($result->errMessage());
        }

        return $result->toArray();
    }

    /**
     * 本地登录模式处理
     * @param \Illuminate\Http\Request $request
     * @return WechatUser
     */
    protected function localDevModeHandle($request)
    {
        $id = (int) $request->input('id');

        /** @var WechatUser $user */
        return with(WechatUser::query()->where('id', $id)->first());
    }
}
