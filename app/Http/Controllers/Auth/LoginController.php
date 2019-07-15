<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseHomeController;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends BaseHomeController{

	/*
	|--------------------------------------------------------------------------
	| Login Controller
	|--------------------------------------------------------------------------
	|
	| This controller handles authenticating users for the application and
	| redirecting them to your home screen. The controller uses a trait
	| to conveniently provide its functionality to your applications.
	|
	*/

	use AuthenticatesUsers {
		showLoginForm as protected _showLoginForm;
	}

	/**
	 * Where to redirect users after login.
	 *
	 * @var string
	 */
	protected $redirectTo = '/';

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct(){
		$this->middleware('guest')->except('logout');
	}

	public function showLoginForm(){
		$this->setMeta('登录');
		return $this->_showLoginForm();
	}
}
