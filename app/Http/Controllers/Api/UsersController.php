<?php
/**
 * The following code, none of which has BUG.
 *
 * @author: BD<liuxingwu@duoguan.com>
 * @date: 2019/7/12 15:49
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UsersController extends Controller{

	/**
	 * 用户列表
	 *
	 * @return \Illuminate\Support\Collection
	 */
	public function index(){
		//		$data = DB::select('select * from la_users limit 0,100');
		$data = DB::table('users')->paginate(20);
		return $data;
	}

	/**
	 * 生产用户数据
	 *
	 * @return array
	 */
	public function builds(){
		$result = [];
		for($i = 0; $i < rand(1000, 99999); $i++){
			//			DB::insert('insert into la_users ( name , create_time ) values ( ? , ?)', [
			//				Str::uuid(), date('Y-m-d H:i:s'),
			//			]);
			$item = [
				'gender'      => rand(0, 2),
				'name'        => Str::uuid(),
				'create_time' => date('Y-m-d H:i:s'),
			];
			$item['id'] = DB::table('users')->insertGetId($item);
			$result[] = $item;
		}
		return $result;
	}

	/**
	 * 获取用户信息
	 *
	 * @param $id
	 * @return array|null
	 */
	public function show($id){
		//		$data = DB::select('select * from la_users where `id` = ? limit 0,1', [$id]);
		$info = DB::table('users')->where('id', $id)->first();
		return empty($info) ? null : (array)$info;
	}
}
