<?php

namespace App\Http\Controllers\Admin;

use App\Facades\View;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends BaseController{

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
        $this->assignCategories();
        return view('posts.edit');
    }

    /**
     * 赋值分类列表
     */
    private function assignCategories(){
        $data = Category::where('status', 1)->select('id', 'pid', 'title')->get();
        View::share('categories', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request){
        $data = $request->validate([
            'title'       => 'required|unique:posts|max:255',
            'description' => 'required|min:15|max:128',
            'content'     => 'required',
            'category_id' => 'required|exists:categories,id',
        ]);
        $data['uid'] = 1;
        $request->has('view_count') && $data['view_count'] = intval($request->input('view_count'));
        $request->has('praise_count') && $data['praise_count'] = intval($request->input('praise_count'));
        $request->has('comment_count') && $data['view_count'] = intval($request->input('comment_count'));

        Post::create($data);

        return redirect()->route('home');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id){
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id){
        //
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
