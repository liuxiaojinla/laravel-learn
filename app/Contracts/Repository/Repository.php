<?php

namespace App\Contracts\Repository;

interface Repository
{
    const SCENE_FILTER = 'filter';

    const SCENE_DETAIL = 'detail';

    const SCENE_SHOW = 'show';

    const SCENE_STORE = 'store';

    const SCENE_UPDATE = 'update';

    const SCENE_DELETE = 'delete';

    const SCENE_RECOVERY = 'recovery';

    const SCENE_RESTORE = 'restore';

    const SCENE_VALIDATE = 'validate';

    /**
     * @param array $search
     * @param array $with
     * @param array $options
     * @return mixed
     */
    public function lists($search = [], $with = [], array $options = []);

    /**
     * @param array $search
     * @param array $with
     * @param int $paginate
     * @param array $options
     * @return mixed
     */
    public function paginate($search = [], $with = [], $paginate = 1, array $options = []);

    /**
     * @param mixed $filter
     * @param array $with
     * @param array $options
     * @return mixed
     */
    public function filter($filter = [], array $with = [], array $options = []);

    /**
     * @param int $id
     * @param array $with
     * @return mixed
     */
    public function detail($id, $with = [], array $options = []);

    /**
     * @param int $id
     * @param array $with
     * @param array $options
     * @return mixed
     */
    public function show($id, $with = [], array $options = []);

    /**
     * @param array $data
     * @return mixed
     */
    public function store($data, array $options = []);

    /**
     * @param int $id
     * @param array $data
     * @return mixed
     */
    public function update($id, $data, array $options = []);

    /**
     * @param array $ids
     * @return mixed
     */
    public function delete($ids, array $options = []);

    /**
     * @param array $ids
     * @return mixed
     */
    public function recovery($ids, array $options = []);

    /**
     * @param array $ids
     * @return mixed
     */
    public function restore($ids, array $options = []);

    /**
     * @param array $data
     * @param string $scene
     * @return mixed
     */
    public function validate($data, $scene = null, array $options = []);
}
