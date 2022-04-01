<?php

namespace App\Http\Controllers\Testing;

use App\Http\Controllers\Controller;
use App\Http\RepositoryHandlers\GlobalMiddlewareHandler;
use App\Http\RepositoryHandlers\MiddlewareHandler;
use App\Models\User;
use App\Repository\Repository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use function app;
use function App\Http\Controllers\dd;
use function dump;
use function now;

class RepositoryController extends Controller
{
    /**
     * @var Repository
     */
    protected $repository;

    public function __construct()
    {
        Repository::globalFilterable(function ($input, $next) {
            dump('global filter');

            return $next($input);
        });

        Repository::setupGlobalHandler(GlobalMiddlewareHandler::class);
    }

    public function lists()
    {
        $this->repository()->filterable(function ($input, $next) {
            dump('temp filterable.');

            /** @var Builder $query */
            $query = $input['query'];
            $query->where('name', '<>', 1);

            /** @var Collection $data */
            $data = $next($input);

            return $data->each(function (User $user) {
                $user->name .= '-' . now();
            });
        });

        $data = $this->repository()->lists();

        dd($data);
    }

    public function paginate()
    {
        $this->repository()->filterable(function ($input, $next) {
            dump('temp filterable.');

            /** @var Builder $query */
            $query = $input['query'];
            $query->where('name', '<>', 1);

            /** @var Collection $data */
            $data = $next($input);

            $data->each(function (User $user) {
                $user->name .= '-' . now();
            });

            return $data;
        });

        $data = $this->repository()->paginate();

        dd($data);
    }

    public function detail(Request $request)
    {
        $id = $request->input('id', 1);
        $id = intval($id);

        $this->repository()->detailable(function ($input, $next) use ($id) {
            dump('temp detailable.');

            /** @var Builder $query */
            $query = $input['query'];

            // $query->where('name', '<>', $id);

            return $next($input);
        });
        $info = $this->repository()->detail($id);

        dd($info);
    }

    public function show(Request $request)
    {
        $id = $request->input('id', 1);
        $id = intval($id);

        $this->repository()->showable(function ($input, $next) use ($id) {
            dump('temp showable.');

            /** @var Builder $query */
            $query = $input['query'];
            $query->where('name', '<>', $id);

            return $next($input);
        });
        $info = $this->repository()->show($id);

        dd($info);
    }

    public function store(Request $request)
    {
        $this->repository()->storeable(function ($input, $next) {
            dump('temp storeable.');

            $input['data']['name'] .= Str::uuid()->toString();

            $model = $next($input);
            $model->isShow = true;

            return $model;
        });
        $model = $this->repository()->store([
            'name' => now()->toDateString(),
        ]);
        dd($model);
    }

    public function update(Request $request)
    {
        $id = $request->input('id', 1);
        $id = intval($id);

        $this->repository()->updateable(function ($input, $next) {
            dump('temp updateable.');

            $input['data']['name'] .= 'update';

            $model = $next($input);
            $model->isShow = true;

            return $model;
        });
        $model = $this->repository()->update($id, [
            'name' => now()->toDateString(),
        ]);
        dd($model);
    }

    public function delete(Request $request)
    {
        $id = $request->input('id', User::query()->first()['id']);
        $id = intval($id);

        $this->repository()->deleteable(function ($input, $next) {
            dump('temp deleteable.');

            return $next($input);
        });
        $result = $this->repository()->delete([$id]);
        dd($result);
    }

    public function recovery(Request $request)
    {
        $id = $request->input('id', 1);
        $id = intval($id);

        $this->repository()->recoveryable(function ($input, $next) {
            dump('temp recoveryable.');

            return $next($input);
        });
        $result = $this->repository()->recovery([10]);

        dd($result);
    }

    public function restore(Request $request)
    {
        $id = $request->input('id', 1);
        $id = intval($id);

        $this->repository()->restoreable(function ($input, $next) {
            dump('temp restore.');

            return $next($input);
        });
        $result = $this->repository()->restore([10]);

        dd($result);
    }

    public function __invoke($action)
    {
        if (method_exists($this, $action)) {
            return app()->call([$this, $action]);
        }

        throw new NotFoundHttpException("Bad method $action.");
    }

    private function repository()
    {
        if ($this->repository == null) {
            $this->repository = new Repository([
                'model' => User::class,
                'handler' => MiddlewareHandler::class,
            ]);
        }

        return $this->repository;
    }
}
