<?php
/**
 * The following code, none of which has BUG.
 *
 * @author: BD<liuxingwu@duoguan.com>
 * @date: 2019/7/15 11:01
 */

namespace App\Http\Controllers\Managers;

use App\Http\Controllers\BaseHomeController;
use App\Models\Category;
use Illuminate\Http\Request;

class CategorysController extends BaseHomeController{

	/**
	 * PostsController constructor.
	 */
	public function __construct(){
		$this->middleware('auth');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(){
		//
	}

	/**
	 * 发布文章
	 *
	 * @return \Illuminate\Contracts\View\View
	 */
	public function create(){
		return $this->setMeta('创建分类')->fetch('categorys.edit');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request){
		$data = $request->validate([
			'title'       => 'required|unique:posts|max:255',
			'keywords'    => 'required|min:3|max:48',
			'description' => 'required|min:15|max:128',
			'content'     => 'required',
		]);
		$data['uid'] = 1;
		Category::create($data);
		return redirect()->route('home');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param int $id
	 * @return \Illuminate\Contracts\View\View
	 */
	public function show($id){
		/** @var Category $info */
		$info = Category::findOrFail($id);
		return $this->setMeta($info->title)->fetch('index.info', [
			'info' => $info,
		]);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param int                      $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id){
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param int $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id){
		//
	}
}
