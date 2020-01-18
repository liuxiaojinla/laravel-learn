<?php

namespace App\Http\Controllers;

use App\Models\Post;

class PostController extends BaseController{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index(){
        $data = Post::latest()->paginate(15);
        return $this->setMeta('首页')->make('index.index', [
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
        /** @var Post $info */
        $info = Post::findOrFail($id);
        $info->increment('view_count');
        return $this->setMeta($info->title)->make('index.info', [
            'info' => $info,
        ]);
    }
    //    /**
    //     * 发布文章
    //     *
    //     * @return \Illuminate\Contracts\View\View
    //     */
    //    public function create(){
    //        $this->assignCategorys();
    //        return $this->setMeta('发布文章')->make('posts.edit');
    //    }
    //
    //    /**
    //     * 赋值分类列表
    //     */
    //    private function assignCategorys(){
    //        $data = Category::where('status', 1)->select('id', 'pid', 'title')->get();
    //        View::share('categorys', $data);
    //    }
    //
    //    /**
    //     * Store a newly created resource in storage.
    //     *
    //     * @param \Illuminate\Http\Request $request
    //     * @return \Illuminate\Http\RedirectResponse
    //     */
    //    public function store(Request $request){
    //        $data = $request->validate([
    //            'title'       => 'required|unique:posts|max:255',
    //            'description' => 'required|min:15|max:128',
    //            'content'     => 'required',
    //            'category_id' => 'required|exists:categories,id',
    //        ]);
    //        $data['uid'] = 1;
    //        $request->has('view_count') && $data['view_count'] = intval($request->input('view_count'));
    //        $request->has('praise_count') && $data['praise_count'] = intval($request->input('praise_count'));
    //        $request->has('comment_count') && $data['view_count'] = intval($request->input('comment_count'));
    //
    //        Post::create($data);
    //
    //        return redirect()->route('home');
    //    }

}
