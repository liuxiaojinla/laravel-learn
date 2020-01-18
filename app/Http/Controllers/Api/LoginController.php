<?php
/**
 * The following code, none of which has BUG.
 *
 * @author: BD<657306123@qq.com>
 * @date: 2020/1/18 14:03
 */

namespace App\Http\Controllers\Api;

use App\Contracts\LoginException;
use App\Foundation\Hint;
use App\Http\Controllers\Api\login\Wechat;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;

class LoginController extends BaseController{

    /**
     * @var \App\Contracts\LoginInterface
     */
    private $login;

    /**
     * LoginController constructor.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function __construct(Application $app){
        $this->login = $app->make(Wechat::class);
    }

    /**
     * 登录
     *
     * @param \Illuminate\Http\Request $request
     */
    public function __invoke(Request $request){
        $user = $request->post('user');
        $pwd = $request->post('pwd');

        try{
            $user = $this->login->login($user, $pwd);
            $this->fireEvent($user);
            return Hint::success($user);
        }catch(LoginException $e){
            return Hint::error($e->getMessage(), $e->getCode());
        }
    }

    private function fireEvent(User $user){
    }

}
