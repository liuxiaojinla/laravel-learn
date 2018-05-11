<?php
/**
 * I know no such things as genius,it is nothing but labor and diligence.
 *
 * @author <657306123@qq.com> 北斗
 */

namespace App\Http\Controllers;

/**
 * 用户控制器
 *
 * @package App\Http\Controllers
 */
class UserController extends Controller{

	/**
	 * UserController constructor.
	 */
	public function __construct(){
		$this->middleware([]);
	}

	public function showProfile(){
		return 'showProfile: '.route('profile');
	}
}