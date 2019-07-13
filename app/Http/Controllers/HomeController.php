<?php

namespace App\Http\Controllers;

class HomeController extends BaseHomeController{

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct(){
		$this->middleware('auth');
	}

	/**
	 * Show the application dashboard.
	 *
	 * @return \Illuminate\Contracts\Support\Renderable
	 */
	public function index(){
		return $this->setMeta('首页')->fetch('home');
//		return view('home');
	}
}
