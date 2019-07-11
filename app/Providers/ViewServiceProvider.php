<?php

namespace App\Providers;

use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider{

	/**
	 * 获取由提供者提供的服务。
	 *
	 * @return array
	 */
	public function provides(){
		var_dump(ViewFactory::class);
		return [ViewFactory::class];
	}

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

		// 注册视图指令
		$this->registerBladeDirectives();

		// 禁用双重编码
		//		Blade::withoutDoubleEncoding();
	}

	/**
	 * 注册组件别名
	 */
	private function registerBladeComponentsAlias(){
		Blade::component('components.alert', 'alert');
		Blade::component('components.panel', 'panel');
	}

	/**
	 * 注册包含视图别名
	 */
	private function registerBladeIncludeAlias(){
		Blade::include('includes.input', 'input');
	}

	/**
	 * 注册指令别名
	 */
	private function registerBladeDirectives(){
		Blade::directive('datetime', function($expression){
			return "<?php echo ($expression)->format('m/d/Y H:i'); ?>";
		});

		Blade::if('env', function ($environment) {
			return app()->environment($environment);
		});
	}

}
