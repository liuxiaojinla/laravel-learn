<?php

namespace Plugins\article\Web\Controllers;

use App\Http\Controllers\Web\Controller;
use Plugins\article\Models\Article;

class IndexController extends Controller
{
    public function index()
    {
        $data = Article::query()->latest()->paginate();

        // Article::factory(1000)->create();

        return view('article::article_list', [
            'data' => $data,
        ]);
    }

    public function detail($id)
    {
        $info = Article::query()->where('id', $id)->first();

        return view('article::article_detail', [
            'info' => $info,
        ]);
    }
}
