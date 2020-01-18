<?php

namespace App\Http\Controllers;

use App\Foundation\Hint;
use App\Models\Post;

class HomeController extends BaseController{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(){
        $data = Post::latest()->paginate(15);
        return $this->setMeta('首页')->make('index.index', [
            'data' => $data,
        ]);
    }
}
