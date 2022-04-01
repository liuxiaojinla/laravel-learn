<?php

namespace App\Contracts\Uploader;

interface DataProvider
{
    /**
     * @param string $scene
     * @param string $md5
     * @return array
     */
    public function getByMd5($scene, $md5);

    /**
     * @param string $scene
     * @param string $sha1
     * @return array
     */
    public function getBySha1($scene, $sha1);

    /**
     * @param string $scene
     * @param array $data
     * @return array
     */
    public function save($scene, array $data);
}