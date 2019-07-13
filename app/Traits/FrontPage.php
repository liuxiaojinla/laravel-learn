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
			'_META' => [
				'title'       => $title,
				'description' => $description,
				'keywords'    => $keywords,
			],
		]);

		return $this;
	}
}
