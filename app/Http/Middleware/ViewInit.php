<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;

class ViewInit{

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     * @return mixed
     */
    public function handle($request, Closure $next){
        $this->initVariable();

        $this->initBlade();

        $this->initPaginator();

        return $next($request);
    }

    /**
     * 初始化模板公用变量
     */
    private function initVariable(){
        View::share([
            //			'_meta_title'       => '',
            //			'_meta_description' => '',
            //			'_meta_keywords'    => '',
        ]);
    }

    /**
     * 初始化视图
     */
    private function initBlade(){
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

        Blade::if('env', function($environment){
            return app()->environment($environment);
        });
    }

    /**
     * 初始化分页器模板
     */
    private function initPaginator(){
        //		Paginator::defaultView('view-name');
        //		Paginator::defaultSimpleView('view-name');
    }

}
