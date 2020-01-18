<?php
/**
 * The following code, none of which has BUG.
 *
 * @author: BD<liuxingwu@duoguan.com>
 * @date: 2019/7/15 11:01
 */

namespace App\Http\Controllers\User;

use App\Http\Controllers\BaseController;
use App\Models\Category;

class CategoryController extends BaseController{

    /**
     * PostsController constructor.
     */
    public function __construct(){
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index(){
        $data = Category::latest()->paginate(15);
        return $this->setMeta('专题列表')->make('category.index', [
            'data' => $data,
        ]);
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

        return $this->setMeta($info->title)->make('category.info', [
            'info' => $info,
        ]);
    }

}
