<?php

namespace App\Http\Controllers\Manager;

use Illuminate\Http\Request;

/**
 * 当前授权用户
 */
class CurrentAuthUserController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function info(Request $request)
    {
        return success($request->user());
    }
}
