<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware{

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    protected function redirectTo($request){
        if($request->expectsJson()){
            return '';
        }

        if($this->isAdmin($request)){
            dd($request->route());
            return route('admin.login');
        }else{
            return route('login');
        }
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    private function isAdmin(Request $request){
        $path = $request->path();
        return $path == "admin" || strpos($path, "admin/") === 0;
    }
}
