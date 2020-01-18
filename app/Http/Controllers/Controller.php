<?php

namespace App\Http\Controllers;

use App\Models\Post;

class Controller extends BaseController{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){
        //		$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(){
        $data = Post::latest()->paginate(15);
        return $this->setMeta('首页')->fetch('index.index', [
            'data' => $data,
        ]);
        //		return view('home');
    }
}
