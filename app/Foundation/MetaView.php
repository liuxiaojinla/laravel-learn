<?php
/**
 * The following code, none of which has BUG.
 *
 * @author: BD<657306123@qq.com>
 * @date: 2020/1/18 13:59
 */

namespace App\Foundation;

use App\Facades\View;

trait MetaView{

    protected function setMeta($title, $description = null, $keywords = null){
        return View::setMeta($title, $description, $keywords);
    }
}
