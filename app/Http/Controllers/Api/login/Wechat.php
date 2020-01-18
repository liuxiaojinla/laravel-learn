<?php
/**
 * The following code, none of which has BUG.
 *
 * @author: BD<657306123@qq.com>
 * @date: 2020/1/18 14:31
 */

namespace App\Http\Controllers\Api\login;

use App\Contracts\LoginInterface;
use App\Models\User;

class Wechat implements LoginInterface{

    /**
     * @inheritDoc
     */
    public function login($user, $pwd, $type = 0){
        // TODO: Implement login() method.
    }
}
