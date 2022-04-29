<?php

namespace App\Http\Controllers\Testing;

use App\Foundation\Controller\PageCURD;
use App\Http\Controllers\Controller;
use Plugins\article\Models\Article;

class PageCURDController extends Controller
{
    use PageCURD;

    /**
     * @return string
     */
    protected function repositoryTo()
    {
        return Article::class;
        // return app(RepositoryManager::class)->repository(Article::class);
    }


    /**
     * @param $input
     * @param callable $next
     * @return mixed
     */
    protected function filterable($input, callable $next)
    {
        return $next($input);
    }
}