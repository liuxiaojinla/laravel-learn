<?php
/**
 * The following code, none of which has BUG.
 *
 * @author: BD<657306123@qq.com>
 * @date: 2020/1/18 11:04
 */

namespace App\Concerns;

trait ConstProperty{

    /**
     * 常量列表
     *
     * @var array
     */
    private $consts = [];

    /**
     * 初始化常量列表
     *
     * @param array $consts
     */
    protected function initializeConstProperty(array $consts){
        $this->consts = $consts;
    }

    /**
     * 获取属性
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name){
        return $this->consts[$name];
    }

    public function __set($name, $value){
        if(isset($this->consts[$name])){
            throw new \UnexpectedValueException("Cannot assign to {$name} constant.");
        }
    }

    /**
     * 检查常量是否存在
     *
     * @param string $name
     * @return bool
     */
    public function __isset($name){
        return isset($this->consts[$name]);
    }

}
