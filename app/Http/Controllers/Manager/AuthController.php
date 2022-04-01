<?php

namespace App\Http\Controllers\Manager;

use App\Exceptions\ValidationException;
use App\Models\Wechat\WechatWorkUser;
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
     */
    public function login(Request $request)
    {
        $deviceName = $request->input('device_name', 'web');
        $code = (string) $request->input('code');

        $user = null;

        if ($code) {
            $user = $this->loginByMiniProgramCode($code);
        }

        // 本地开发模式兼容
        if (app()->environment('local') && !$code) {
            $user = $this->localDevModeHandle($request);
        }

        if (!$user) {
            throw ValidationException::withMessage('你不是系统管理员，没有使用权限！');
        }

        $accessToken = $user->createToken($deviceName);

        return success(array_merge([
            'user' => $user,
        ], $accessToken->toArray()));
    }

    /**
     * 企微小程序登录
     * @param string $code
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     * @throws \App\Exceptions\ValidationException
     */
    protected function loginByMiniProgramCode($code)
    {
        $authInfo = $this->getQyAuthInfo($code);

        /** @var WechatWorkUser $user */
        return WechatWorkUser::query()->where([
            'userid' => $authInfo['userid'],
            'corp_id' => $authInfo['corpid'],
        ])->first();
    }

    /**
     * 获取企业微信授权信息
     * @param string $code
     * @return array
     * @throws \App\Exceptions\ValidationException
     */
    protected function getQyAuthInfo(string $code)
    {
        $result = WechatResult::make(WeWork::openwork()->mini_program->session($code));
        if (!$result->isSucceeded()) {
            Log::error("B端登录失败：", [
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
     * @return WechatWorkUser
     */
    protected function localDevModeHandle($request)
    {
        $id = (int) $request->input('id');

        /** @var WechatWorkUser $user */
        return with(WechatWorkUser::query()->where('id', $id)->first());
    }
}
