<?php
/**
 * The following code, none of which has BUG.
 *
 * @author: BD<657306123@qq.com>
 * @date: 2020/1/18 17:21
 */

namespace App\Exceptions;

class HttpJumpException extends \Exception{

    /**
     * @var array
     */
    private $data;

    /**
     * @return array
     */
    public function &getData(){
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData(array $data){
        $this->data = $data;
    }
}
