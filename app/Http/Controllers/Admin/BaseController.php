<?php
/**
 * The following code, none of which has BUG.
 *
 * @author: BD<657306123@qq.com>
 * @date: 2020/1/18 15:09
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Routing\Controller;

class BaseController extends Controller{

    /**
     * BaseController constructor.
     */
    public function __construct(){
        $this->middleware('auth:admin');
    }
}
