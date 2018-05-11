<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/**
 * 输出业务状态
 *
 * @param int    $code 状态码
 * @param string $msg 错误信息
 * @param mixed  $data 业务结果
 * @param array  $extra 扩展字段
 * @return string
 */
function output($code, $msg, $data, $extra = array()){
	return response()->json(array_merge($extra, array(
		'code' => $code,
		'msg' => $msg,
		'data' => $data,
	)));
}

/**
 * 输出业务状态
 *
 * @param int    $code 状态码
 * @param string $msg 错误信息
 * @param array  $extra 扩展字段
 * @return string
 */
function output_error($msg, $code = 0, $extra = array()){
	return output($code, $msg, array(), $extra);
}

/**
 * 输出业务状态
 *
 * @param mixed $data 业务结果
 * @param array $extra 扩展字段
 * @return string
 */
function output_success($data = array(), $extra = array()){
	return output(1, 'success', $data, $extra);
}

Route::middleware('auth:api')->get('/user', function(Request $request){
	return $request->user();
});

//路由模型
Route::get('users/{user}', function(\App\User $user){
	return output_success($user);
});

Route::post('upload', function(\Illuminate\Http\Request $request){
	if($request->hasFile('file') && $request->file('file')->isValid()){
		$photo = $request->file('file');
		$extension = $photo->extension();
		$store_result = $photo->store('photo'.'/'.date('Y').'/'.date('md'));
		//		$store_result = $photo->storeAs('file', 'test.jpg');
		$output = [
			'extension' => $extension,
			'store_result' => $store_result,
		];
		return output_success($output);
	}
	return output_error('未获取到上传文件或上传过程出错');
});