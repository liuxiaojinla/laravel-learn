<?php

namespace App\Support\Facades;

use App\Services\Hint\HintManager;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Facade;

/**
 * @see HintManager
 * @method static Response result(mixed $data, array $extend = [])
 * @method static Response success(string $msg = null, $data = null, array $extend = [])
 * @method static Response error(string $msg = null, int $code = null, $data = null, array $extend = [])
 * @method static Response alert(string $msg = null, string $title = null, int $code = null, $data = null, array $extend = [])
 * @method static void outputResult($data, array $extend = [], callable $callback = null) throws HttpResponseException
 * @method static void outputSuccess(string $msg = null, $data = null, array $extend = [], callable $callback = null) throws HttpResponseException
 * @method static void outputError(string $msg = null, int $code = 0, $data = null, array $extend = [], callable $callback = null) throws HttpResponseException
 * @method static void outputAlert(string $msg = null, string $title = null, int $code = null, array $extend = [], callable $callback = null) throws HttpResponseException
 */
class Hint extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'hint';
    }
}