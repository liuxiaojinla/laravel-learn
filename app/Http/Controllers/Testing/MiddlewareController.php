<?php

namespace App\Http\Controllers\Testing;

use App\Http\Controllers\Controller;
use App\Services\Middleware\MiddlewareManager;
use function App\Http\Controllers\dd;
use function dump;

class MiddlewareController extends Controller
{
    public function __invoke()
    {
        $middlewareManager = new MiddlewareManager();
        $middlewareManager->push('global', function ($input, $next) {
            $input['name'] = 'Word';
            dump('第一个：insert');

            return $next($input);
        });
        $middlewareManager->push('global', function ($input, $next) {
            dump('第二个：insert');
            dump($input);

            return $next($input);
        });
        $response = $middlewareManager->then([], function ($input) {
            return 'hello :' . ($input['name'] ?? '');
        });
        dd($response);
    }
}