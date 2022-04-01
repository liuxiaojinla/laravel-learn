<?php

namespace App\Foundation\Hint;

trait InteractsWithHint
{
    protected function result($data, array $extend = [])
    {
        return app('hint')->result($data, $extend);
    }

    protected function success(string $msg = null, $data = null, array $extend = [])
    {
        return app('hint')->success($msg, $data, $extend);
    }

    protected function error(string $msg = null, int $code = null, $data = null, array $extend = [])
    {
        return app('hint')->error($msg, $code, $data, $extend);
    }
}