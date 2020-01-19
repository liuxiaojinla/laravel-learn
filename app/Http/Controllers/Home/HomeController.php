<?php

namespace App\Http\Controllers\Home;

use App\Models\Post;

/**
 * Class HomeController
 */
class HomeController extends BaseController{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(){
        $data = Post::latest()->paginate(15);
        return view('home.index.index', [
            'data' => $data,
        ]);
    }
}
