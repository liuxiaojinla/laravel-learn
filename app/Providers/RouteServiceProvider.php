<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider{

	/**
	 * This namespace is applied to your controller routes.
	 * In addition, it is set as the URL generator's root namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'App\Http\Controllers';

	/**
	 * This namespace is applied to your controller routes.
	 * In addition, it is set as the URL generator's root namespace.
	 *
	 * @var string
	 */
	protected $apiNamespace = 'App\Http\Controllers\Api';

	/**
	 * Define your route model bindings, pattern filters, etc.
	 *
	 * @return void
	 */
	public function boot(){
		// 定义全局变量规则
		Route::pattern('id', '[0-9]+');

		parent::boot();
	}

	/**
	 * Define the routes for the application.
	 *
	 * @return void
	 */
	public function map(){
		//Api 路由
		$this->mapApiRoutes();
		// PC端路由
		$this->mapWebRoutes();
	}

	/**
	 * Define the "api" routes for the application.
	 * These routes are typically stateless.
	 *
	 * @return void
	 */
	protected function mapApiRoutes(){
		Route::prefix('api')
			->middleware('api')
			->namespace($this->apiNamespace)
			->group(base_path('routes/api.php'));
	}

	/**
	 * Define the "web" routes for the application.
	 * These routes all receive session state, CSRF protection, etc.
	 *
	 * @return void
	 */
	protected function mapWebRoutes(){
		Route::middleware('web')
			->namespace($this->namespace)
			->group(base_path('routes/web.php'));
	}
}
