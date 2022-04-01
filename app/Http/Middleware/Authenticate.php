<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Models\Wechat\WechatWorkUser;
use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * The names of the cookies that should not be encrypted.
     *
     * @var array<int, string>
     */
    protected array $except = [
        'api/auth/login',
        'manager/auth/login',
    ];

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string[] ...$guards
     * @return mixed
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $module = $request->module();

        if (!$request->is($this->except)) {
            //            $this->setTemporaryUser($module);

            $this->authenticate($request, $guards);
        }

        return $next($request);
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param \Illuminate\Http\Request $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (!$request->expectsJson()) {
            return url('login');
        }
    }

    /**
     * 设置临时用户
     * app_env 环境必须是 local
     * @param string $module
     * @return void
     */
    protected function setTemporaryUser($module)
    {
        if (!app()->environment('local')) {
            return;
        }

        if ('api' == $module) {
            /** @var User $user */
            $user = User::query()->first();
        } else {
            /** @var WechatWorkUser $user */
            $user = WechatWorkUser::query()->where('role_id', '<>', 0)->first();
        }

        $this->auth->guard()->setUser($user);
    }
}
