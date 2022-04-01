<?php

namespace App\Foundation\Controller;

use App\Services\Repository\Repository;
use App\Services\Repository\RepositoryManager;
use App\Support\Facades\Hint;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * @method mixed filterable($input, callable $next)
 * @method mixed detailable($input, callable $next)
 * @method mixed validateable($input, callable $next)
 * @method mixed storeable($input, callable $next)
 * @method mixed showable($input, callable $next)
 * @method mixed updateable($input, callable $next)
 * @method mixed deleteable($input, callable $next)
 * @method mixed recoveryable($input, callable $next)
 * @method mixed restoreable($input, callable $next)
 */
trait CURD
{
    /**
     * @return mixed
     * @noinspection PhpReturnDocTypeMismatchInspection
     */
    public function index()
    {
        $data = $this->attachHandler('filterable')
            ->repository()->paginate();

        return $this->renderIndex($data);
    }

    /**
     * @param Collection $data
     * @return \Illuminate\Http\Response
     */
    protected function renderIndex($data)
    {
        return Hint::result($data);
    }

    /**
     * @param Request $request
     * @return mixed
     * @noinspection PhpReturnDocTypeMismatchInspection
     */
    public function detail(Request $request)
    {
        $id = (int) $request->input('id', 0);

        $info = $this->attachHandler('detailable')
            ->repository()->detail($id);

        return $this->renderDetail($info);
    }

    /**
     * @param Model $info
     * @return \Illuminate\Http\Response
     */
    protected function renderDetail($info)
    {
        return Hint::result($info);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->input();

        $info = $this->attachHandler(['validateable', 'storeable'])
            ->repository()->store($data);

        return Hint::success('创建成功！', $info);
    }

    /**
     * @param Request $request
     * @return mixed
     * @noinspection PhpReturnDocTypeMismatchInspection
     */
    public function show(Request $request)
    {
        $id = (int) $request->input('id', 0);

        $info = $this->attachHandler('showable')
            ->repository()->show($id);

        return $this->renderShow($info);
    }

    /**
     * @param Model $info
     * @return \Illuminate\Http\Response
     */
    protected function renderShow($info)
    {
        return Hint::result($info);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $id = (int) $request->input('id', 0);
        $data = $request->input();

        $info = $this->attachHandler(['validateable', 'updateable'])
            ->repository()->update($id, $data);

        return Hint::success('保存成功！', $info);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $ids = $this->readIdList($request);

        $result = $this->attachHandler('deleteable')
            ->repository()->delete($ids);

        return Hint::success('删除成功！', $result);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function recovery(Request $request)
    {
        $ids = $this->readIdList($request);

        $result = $this->attachHandler('recoveryable')
            ->repository()->recovery($ids);

        return Hint::success('删除成功！', $result);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function restore(Request $request)
    {
        $ids = $this->readIdList($request);

        $result = $this->attachHandler('restoreable')
            ->repository()->restore($ids);

        return Hint::success('恢复成功！', $result);
    }

    /**
     * @param Request $request
     * @return array
     */
    protected function readIdList(Request $request)
    {
        $ids = $request->input('ids', '');
        if (!is_array($ids)) {
            $ids = explode(',', $ids);
        }

        return array_values(array_filter($ids, 'intval'));
    }

    /**
     * @param string|array $scenes
     * @return $this
     */
    protected function attachHandler($scenes)
    {
        $scenes = is_array($scenes) ? $scenes : [$scenes];
        $thisRef = new \ReflectionClass($this);

        $repository = $this->repository();
        foreach ($scenes as $scene) {
            if ($thisRef->hasMethod($scene)) {
                $method = $thisRef->getMethod($scene);
                $method->setAccessible(true);
                $repository->$scene($method->getClosure($this));
            }
        }

        return $this;
    }

    /**
     * @return Repository
     */
    protected function repository(): Repository
    {
        $repository = $this->repositoryTo();
        if ($repository instanceof Repository) {
            return $repository;
        }

        return app(RepositoryManager::class)->repository($repository);
    }

    /**
     * @return string|\App\Contracts\Repository\Repository
     */
    abstract protected function repositoryTo();
}
