<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider{

	/**
	 * Register services.
	 *
	 * @return void
	 */
	public function register(){
		//
	}

	/**
	 * Bootstrap services.
	 *
	 * @return void
	 */
	public function boot(){
		// 注册组件别名
		$this->registerBladeComponentsAlias();

		// 注册包含视图别名
		$this->registerBladeIncludeAlias();

		// 禁用双重编码
		//		Blade::withoutDoubleEncoding();
	}

	/**
	 * 注册组件别名
	 */
	private function registerBladeComponentsAlias(){
		Blade::component('components.alert', 'alert');
	}

	/**
	 * 注册包含视图别名
	 */
	private function registerBladeIncludeAlias(){
		Blade::include('includes.input', 'input');
	}
}
