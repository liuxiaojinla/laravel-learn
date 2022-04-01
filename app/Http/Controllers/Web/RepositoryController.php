<?php

namespace App\Http\Controllers\Web;

use App\Contracts\Repository\Repository as RepositoryContract;
use App\Services\Repository\Repository;
use App\Services\Repository\RepositoryManager;
use Plugins\article\Models\Article;

class RepositoryController extends Controller
{
    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(RepositoryManager $repositoryManager)
    {
        $repositoryManager->register(Article::class, function () {
            dump('register...');

            return Repository::ofModel(Article::class);
        });
        $articleRepository = $repositoryManager->repository(Article::class);
        $this->search($articleRepository);
        $repositoryManager->forget(Article::class, true);


        $articleRepository = $repositoryManager->repository(Article::class);
        $this->search($articleRepository);

        dd('sss');
        
        return view('web.home');
    }

    protected function search(RepositoryContract $articleRepository)
    {
        dump($articleRepository);
        dump($articleRepository->lists([
            'keywords' => '意思',
        ]));
        dump($articleRepository->lists([
            'keywords' => '很好',
        ]));
        dump($articleRepository);
    }
}