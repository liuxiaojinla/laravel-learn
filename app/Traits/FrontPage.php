<?php
/**
 * The following code, none of which has BUG.
 *
 * @author: BD<liuxingwu@duoguan.com>
 * @date: 2019/7/13 14:38
 */

namespace App\Traits;

use Illuminate\Support\Facades\View;

trait FrontPage{

	/**
	 * 设置页面元信息
	 *
	 * @param string $title
	 * @param string $description
	 * @param string $keywords
	 * @return static
	 */
	protected function setMeta($title, $description = null, $keywords = null){
		View::share([
			'_meta_title'       => $title,
			'_meta_description' => $description,
			'_meta_keywords'    => $keywords,
		]);

		return $this;
	}

	/**
	 * 编译视图模板
	 *
	 * @param string $view
	 * @param array  $data
	 * @param array  $mergeData
	 * @return \Illuminate\Contracts\View\View
	 */
	protected function fetch($view = null, $data = [], $mergeData = []){
		return View::make($view, $data, $mergeData);
	}
}
