<?php

namespace App\Services\Uploader;

use App\Contracts\Uploader\DataProvider;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class FileDataProvider implements DataProvider
{
    /**
     * @inheritDoc
     */
    public function getByMd5($scene, $md5)
    {
        return (array) $this->query()->where('type', $scene)->where('md5', $md5)->first();
    }

    /**
     * @inheritDoc
     */
    public function getBySha1($scene, $sha1)
    {
        return (array) $this->query()->where('type', $scene)->where('sha1', $sha1)->first();
    }

    /**
     * @inheritDoc
     */
    public function save($scene, array $data)
    {
        $saveData = array_merge([
            'type' => $scene,
        ], Arr::except($data, ['key']));
        $saveData['id'] = $this->query()->insertGetId($saveData);

        return $saveData;
    }

    /**
     * @return \Illuminate\Database\Query\Builder
     */
    protected function query()
    {
        return DB::table('files');
    }
}