<?php
/**
 * The following code, none of which has BUG.
 *
 * @author: BD<657306123@qq.com>
 * @date: 2020/1/19 15:29
 */

namespace App\Http\Controllers\Admin;

class IndexController extends BaseController{

    public function index(){
        return "Hello welcome visit admin.";
    }
}
