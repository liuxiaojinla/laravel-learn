<?php
/**
 * The following code, none of which has BUG.
 *
 * @author: BD<657306123@qq.com>
 * @date: 2020/1/18 14:36
 */

namespace App\Foundation;

use App\Exceptions\HttpJumpException;

final class Hint{

    /**
     * @param string $msg
     * @param int    $code
     * @param mixed  $data
     * @param array  $extend
     * @throws \App\Exceptions\HttpJumpException
     */
    public static function error($msg, $code = 0, $data = [], $extend = []){
        throw self::make($msg, $code, $data, $extend);
    }

    /**
     * @param string $msg
     * @param mixed  $data
     * @param array  $extend
     * @throws \App\Exceptions\HttpJumpException
     */
    public static function success($data, $msg = 'ok', $extend = []){
        throw self::make($msg, 1, $data, $extend);
    }

    /**
     * @param string $msg
     * @param int    $code
     * @param array  $data
     * @param array  $extend
     * @return \App\Exceptions\HttpJumpException
     */
    private static function make($msg, $code = 0, $data = [], $extend = []){
        $e = new HttpJumpException($msg, $code);
        $e->setData(array_merge([
            'data' => $data,
        ], $extend));
        return $e;
    }

}
