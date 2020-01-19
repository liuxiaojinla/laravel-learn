<?php

namespace App\Http\Controllers\Home;

use App\Models\Post;

class PostController extends BaseController{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index(){
        $data = Post::latest()->paginate(15);
        return view('index.index', [
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
        return view('index.info', [
            'info' => $info,
        ]);
    }
}
