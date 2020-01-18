<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class PostController extends BaseController{

    /**
     * PostsController constructor.
     */
    public function __construct(){
        $this->middleware('auth')->except('index');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index(){
        $data = Post::latest()->paginate(15);
        return $this->setMeta('首页')->fetch('index.index', [
            'data' => $data,
        ]);
    }

    /**
     * 发布文章
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create(){
        $this->assignCategorys();
        return $this->setMeta('发布文章')->fetch('posts.edit');
    }

    /**
     * 赋值分类列表
     */
    private function assignCategorys(){
        $data = Category::where('status', 1)->select('id', 'pid', 'title')->get();
        View::share('categorys', $data);
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
     * @return \Illuminate\Contracts\View\View
     */
    public function show($id){
        /** @var Post $info */
        $info = Post::findOrFail($id);
        $info->increment('view_count');
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
