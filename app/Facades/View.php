<?php
/**
 * The following code, none of which has BUG.
 *
 * @author: BD<liuxingwu@duoguan.com>
 * @date: 2019/7/13 14:38
 */

namespace App\Facades;

use Illuminate\Support\Facades\View as BaseViewFacade;

class View extends BaseViewFacade{

    /**
     * 设置页面元信息
     *
     * @param string $title
     * @param string $description
     * @param string $keywords
     * @return \Illuminate\Contracts\View\View
     */
    public static function setMeta($title, $description = null, $keywords = null){
        View::share([
            '_meta_title'       => $title,
            '_meta_description' => $description,
            '_meta_keywords'    => $keywords,
        ]);

        return self::getFacadeRoot();
    }

}
