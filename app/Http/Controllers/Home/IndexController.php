<?php

namespace App\Http\Controllers\Home;

use App\Models\Post;

/**
 * Class HomeController
 */
class IndexController extends BaseController{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(){
        $data = Post::latest()->paginate(15);
        return view('home.post.index', [
            'data' => $data,
        ]);
    }
}
