<?php
/**
 * The following code, none of which has BUG.
 *
 * @author: BD<liuxingwu@duoguan.com>
 * @date: 2019/7/15 11:01
 */

namespace App\Http\Controllers\Home;

use App\Models\Category;

/**
 * Class CategoryController
 */
class CategoryController extends BaseController{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index(){
        $data = Category::latest()->paginate(15);
        return view('home.category.index', [
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

        return view('category.info', [
            'info' => $info,
        ]);
    }

}
