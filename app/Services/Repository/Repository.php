<?php

namespace App\Services\Repository;

use App\Contracts\Repository\Repository as RepositoryContract;
use App\Services\Middleware\MiddlewareManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Repository implements RepositoryContract
{
    use HasMiddleware;

    /**
     * @var array
     */
    protected $options;

    /**
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->options = $options;

        $this->middlewareManager = new MiddlewareManager();

        if (isset($options['handler'])) {
            $this->setupHandler($options['handler']);
        }

        $this->registerSearchMiddleware();
    }

    /**
     * @param string $modelClass
     * @param array $options
     * @return static
     */
    public static function ofModel($modelClass, $options = [])
    {
        return new static([
                'model' => $modelClass,
            ] + $options);
    }

    /**
     * @inerhitDoc
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function lists($search = [], $with = [], array $options = [])
    {
        $options['search'] = $search;
        $options['paginate'] = null;

        return $this->filter(null, $with, $options);
    }

    /**
     * @inerhitDoc
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($search = [], $with = [], $paginate = 1, array $options = [])
    {
        $options['search'] = $search;
        $options['paginate'] = is_array($paginate)
            ? $paginate
            : [
                'page' => $paginate,
            ];

        return $this->filter(null, $with, $options);
    }

    /**
     * 注册搜索中间件
     * @return void
     */
    protected function registerSearchMiddleware()
    {
        $this->filterable(function ($input, $next) {
            $search = $input['options']['search'] ?? [];
            if (empty($search)) {
                return $next($input);
            }

            /** @var Builder $query */
            $query = $input['query'];
            $model = $query->getModel();
            if (method_exists($model, 'search')) {
                $model->search($query, $search);
            }

            return $next($input);
        });
    }

    /**
     * @inerhitDoc
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
     */
    public function filter($filter = null, array $with = [], $options = [])
    {
        $query = $this->query($with, $options);

        if ($filter) {
            if (is_callable($filter)) {
                $filter($query);
            } else {
                $query->where($filter);
            }
        }

        return $this->middleware([
            'type' => static::SCENE_FILTER,
            'filter' => $filter,
            'with' => $with,
            'query' => $query,
            'options' => $options,
        ], function ($input) use ($query) {
            $options = $input['options'] ?? [];

            $paginate = $options['paginate'] ?? false;
            if ($paginate) {
                $paginate = is_array($paginate)
                    ? $paginate
                    : (is_numeric($paginate) ? [
                        'page' => $paginate,
                    ] : []);

                $data = $query->paginate(
                    $paginate['per_page'] ?? null,
                    ['*'],
                    $paginate['page_name'] ?? 'page',
                    $paginate['page'] ?? null
                );
            } else {
                $data = $query->get();
            }

            return $data;
        }, static::SCENE_FILTER);
    }

    /**
     * @inerhitDoc
     * @return Model
     */
    public function detail($id, $with = [], array $options = [])
    {
        $query = $this->query($with, $options)->where('id', $id);

        return $this->middleware([
            'type' => static::SCENE_DETAIL,
            'id' => $id,
            'with' => $with,
            'query' => $query,
            'options' => $options,
        ], function ($input) use ($query, $options) {
            if ($options['fail'] ?? false) {
                return $query->firstOrFail();
            }

            return $query->first();
        }, static::SCENE_DETAIL);
    }

    /**
     * @inerhitDoc
     * @return Model
     */
    public function show($id, $with = [], array $options = [])
    {
        $query = $this->query($with, $options)->where('id', $id);

        return $this->middleware([
            'type' => static::SCENE_SHOW,
            'id' => $id,
            'with' => $with,
            'query' => $query,
            'options' => $options,
        ], function ($input) use ($query, $options) {
            if ($options['fail'] ?? false) {
                return $query->firstOrFail();
            }

            return $query->first();
        }, static::SCENE_SHOW);
    }

    /**
     * @inerhitDoc
     */
    public function validate($data, $scene = null, array $options = [])
    {
        return $this->middleware([
            'type' => static::SCENE_VALIDATE,
            'scene' => $scene,
            'data' => $data,
            'options' => $options,
        ], function ($input) use ($options) {
            return $input['data'] ?? [];
        }, static::SCENE_VALIDATE);
    }

    /**
     * @inerhitDoc
     */
    public function store($data, array $options = [])
    {
        $data = $this->validate($data, static::SCENE_STORE, $options);

        return DB::transaction(function () use ($data, $options) {
            $query = $this->query([], $options);

            return $this->middleware([
                'type' => static::SCENE_STORE,
                'data' => $data,
                'query' => $query,
                'options' => $options,
            ], function ($input) use ($query, $options) {
                return $query->create($input['data'] ?? []);
            }, static::SCENE_STORE);
        });
    }

    /**
     * @inerhitDoc
     */
    public function update($id, $data, $options = [])
    {
        $data = $this->validate($data, static::SCENE_UPDATE);

        return DB::transaction(function () use ($id, $data, $options) {
            $query = $this->query([], $options)->where('id', $id);

            return $this->middleware([
                'type' => static::SCENE_UPDATE,
                'id' => $id,
                'data' => $data,
                'query' => $query,
                'options' => $options,
            ], function ($input) use ($query, $options) {
                if ($options['fail'] ?? true) {
                    $model = $query->firstOrFail();
                } else {
                    $model = $query->first();
                }

                $model->fill($input['data'] ?? [])->save();

                return $model;
            }, static::SCENE_UPDATE);
        });
    }

    /**
     * @inerhitDoc
     */
    public function delete($ids, $options = [])
    {
        return DB::transaction(function () use ($ids, $options) {
            $query = $this->query([], $options)->whereIn('id', $ids);

            return $this->middleware([
                'type' => static::SCENE_DELETE,
                'ids' => $ids,
                'query' => $query,
                'options' => $options,
            ], function ($input) use ($query) {
                return $query->forceDelete();
            }, static::SCENE_DELETE);
        });
    }

    /**
     * @inerhitDoc
     */
    public function recovery($ids = [], $options = [])
    {
        return DB::transaction(function () use ($ids, $options) {
            $query = $this->query([], $options)->whereIn('id', $ids);

            $input = [
                'type' => static::SCENE_RECOVERY,
                'ids' => $ids,
                'query' => $query,
                'options' => $options,
            ];

            return $this->middleware($input, function ($input) use ($query, $options) {
                return $query->delete();
            }, static::SCENE_RECOVERY);
        });
    }

    /**
     * @inerhitDoc
     */
    public function restore($ids, $options = [])
    {
        return DB::transaction(function () use ($ids, $options) {
            /** @var Builder|\Illuminate\Database\Query\Builder $query */
            $query = $this->query([], $options)->withTrashed()->whereIn('id', $ids);

            return $this->middleware([
                'type' => static::SCENE_RESTORE,
                'ids' => $ids,
                'query' => $query,
                'options' => $options,
            ], function ($input) use ($query, $options) {
                return $query->restore();
            }, static::SCENE_RESTORE);
        });
    }

    /**
     * @param array $with
     * @param array $options
     * @return Builder|\Illuminate\Database\Query\Builder
     */
    public function query(array $with = [], $options = [])
    {
        if (isset($this->options['model'])) {
            $modelClass = $this->options['model'];
            /** @var Builder $query */
            $query = call_user_func([$modelClass, 'query']);
            $query->with($with);
        } elseif (isset($this->options['table'])) {
            $table = $this->options['table'];
            $query = DB::table($table);
        } else {
            throw new \RuntimeException('Not support query type.');
        }

        $allowSetOptions = ['select', 'order'];
        foreach ($options as $key => $option) {
            if (!in_array($key, $allowSetOptions)) {
                continue;
            }

            $query->{$key}($option);
        }

        return $query;
    }
}
