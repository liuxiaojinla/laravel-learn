<?php

namespace App\Services\Hint;

use App\Contracts\Hint\Hint;
use App\Services\Service;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;

class ApiHint extends Service implements Hint
{
    /**
     * @inheritDoc
     */
    public function result($data, array $extend = [])
    {
        return $this->response(
            $this->getSuccessCode(),
            $this->getSuccessMsg(),
            $data,
            $extend
        );
    }

    /**
     * @inheritDoc
     */
    public function success(string $msg = null, $data = null, array $extend = [])
    {
        return $this->response(
            $this->getSuccessCode(),
            $msg ?: $this->getSuccessMsg(),
            $data,
            $extend
        );
    }

    /**
     * @inheritDoc
     */
    public function error(string $msg = null, int $code = null, $data = null, array $extend = [])
    {
        return $this->response(
            $code !== null ? $code : $this->getDefaultErrorCode(),
            $msg ?: $this->getDefaultErrorMsg(),
            $data,
            $extend
        );
    }

    /**
     * @inheritDoc
     */
    public function alert(string $msg = null, string $title = null, int $code = null, $data = null, array $extend = [])
    {
        $extend['alert'] = 1;

        return $this->response(
            $code !== null ? $code : $this->getDefaultErrorCode(),
            $msg ?: $this->getDefaultErrorMsg(),
            $data,
            $extend
        );
    }

    /**
     * @inheritDoc
     */
    public function outputResult($data, array $extend = [], callable $callback = null)
    {
        $this->output($this->result($data, $extend), $callback);
    }

    /**
     * @inheritDoc
     */
    public function outputSuccess(string $msg = null, $data = null, array $extend = [], callable $callback = null)
    {
        $this->output($this->success($msg, $data, $extend), $callback);
    }

    /**
     * @inheritDoc
     */
    public function outputError(string $msg = null, int $code = null, $data = null, array $extend = [], callable $callback = null)
    {
        $this->output($this->error($msg, $code, $data, $extend), $callback);
    }

    /**
     * @inheritDoc
     */
    public function outputAlert(string $msg = null, string $title = null, int $code = null, array $extend = [], callable $callback = null)
    {
        $this->output($this->alert($msg, $title, $code, $extend), $callback);
    }

    /**
     * @param JsonResponse $response
     * @param callable|null $callback
     */
    protected function output(JsonResponse $response, callable $callback = null)
    {
        throw new HttpResponseException(tap($response, $callback));
    }

    /**
     * @param int $code
     * @param string $msg
     * @param mixed $data
     * @param array $extend
     * @return JsonResponse
     */
    protected function response(int $code, string $msg, $data, array $extend = []): JsonResponse
    {
        return Response::json(array_merge($extend, [
            'code' => $code,
            'msg' => $msg,
            'data' => $data,
        ]));
    }

    /**
     * @return int
     */
    protected function getSuccessCode(): int
    {
        return $this->getConfig('codes.success') ?: 1;
    }

    /**
     * @return string
     */
    protected function getSuccessMsg(): string
    {
        return $this->getConfig('messages.success') ?: 'success';
    }

    /**
     * @return int
     */
    protected function getDefaultErrorCode(): int
    {
        return $this->getConfig('codes.error') ?: 0;
    }

    /**
     * @return string
     */
    protected function getDefaultErrorMsg(): string
    {
        return $this->getConfig('messages.error') ?: 'error';
    }
}