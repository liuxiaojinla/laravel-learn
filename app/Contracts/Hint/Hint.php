<?php

namespace App\Contracts\Hint;

interface Hint
{
    /**
     * 返回结果
     * @param mixed $data
     * @param array $extend
     * @return mixed
     */
    public function result($data, array $extend = []);

    /**
     * 业务成功
     * @param string|null $msg
     * @param mixed $data
     * @param array $extend
     * @return mixed
     */
    public function success(string $msg = null, $data = null, array $extend = []);

    /**
     * 业务失败
     * @param string|null $msg
     * @param int|null $code
     * @param mixed $data
     * @param array $extend
     * @return mixed
     */
    public function error(string $msg = null, int $code = null, $data = null, array $extend = []);

    /**
     * 业务警告
     * @param string|null $msg
     * @param string|null $title
     * @param int|null $code
     * @param mixed $data
     * @param array $extend
     * @return mixed
     */
    public function alert(string $msg = null, string $title = null, int $code = null, $data = null, array $extend = []);

    /**
     * 输出业务结果
     * @param mixed $data
     * @param array $extend
     * @param callable|null $callback
     */
    public function outputResult($data, array $extend = [], callable $callback = null);

    /**
     * 输出业务成功
     * @param string|null $msg
     * @param mixed $data
     * @param array $extend
     * @param callable|null $callback
     */
    public function outputSuccess(string $msg = null, $data = null, array $extend = [], callable $callback = null);

    /**
     * 输出业务失败
     * @param string|null $msg
     * @param int $code
     * @param mixed $data
     * @param array $extend
     * @param callable|null $callback
     */
    public function outputError(string $msg = null, int $code = 0, $data = null, array $extend = [], callable $callback = null);

    /**
     * 输出业务警告
     * @param string|null $msg
     * @param string|null $title
     * @param int|null $code
     * @param array $extend
     * @param callable|null $callback
     */
    public function outputAlert(string $msg = null, string $title = null, int $code = null, array $extend = [], callable $callback = null);
}