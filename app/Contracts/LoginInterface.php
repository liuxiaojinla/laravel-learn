<?php
/**
 * The following code, none of which has BUG.
 *
 * @author: BD<657306123@qq.com>
 * @date: 2020/1/18 14:03
 */

namespace App\Contracts;

use App\Models\User;

interface LoginInterface{

    /**
     * 登录
     *
     * @param mixed $user
     * @param mixed $pwd
     * @param int   $type
     * @return User
     */
    public function login($user, $pwd, $type = 0);

}
