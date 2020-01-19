<?php

namespace App\Http\Controllers\Home;

use App\Models\User;

class UserController extends BaseController{

    /**
     * 个人信息
     */
    public function show(){
        $uid = 1;
        $user = User::findOrFail($uid);
        return view('user.profile', ['user' => $user]);
    }
}
