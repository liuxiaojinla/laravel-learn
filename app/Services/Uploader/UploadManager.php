<?php

namespace App\Services\Uploader;

use App\Contracts\Uploader\Factory;
use App\Contracts\Uploader\Uploader;
use App\Services\Manager;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Support\Arr;

class UploadManager extends Manager implements Factory, Uploader
{
    /**
     * @var \Illuminate\Filesystem\FilesystemManager
     */
    protected $filesystemManager;

    /**
     * @param \Illuminate\Filesystem\FilesystemManager $filesystemManager
     * @param array $config
     */
    public function __construct(FilesystemManager $filesystemManager, array $config = [])
    {
        parent::__construct($config);
        $this->filesystemManager = $filesystemManager;
    }

    /**
     * 上传文件
     * @param string $scene
     * @param \SplFileInfo $file
     * @param array $options
     * @return array
     */
    public function file($scene, \SplFileInfo $file, array $options = [])
    {
        $dataProvider = $this->getProvider($scene);

        $md5 = md5_file($file->getRealPath());
        $info = $dataProvider->getByMd5($scene, $md5);
        if (!$info) {
            $info = $this->uploader($scene)->file($scene, $file, $options);
            $info = $dataProvider->save($scene, $info);
        }

        return method_exists($dataProvider, 'renderData') ? $dataProvider->renderData($info) : $info;
    }

    /**
     * 获取上传令牌
     * @param string $scene
     * @param string $filename
     * @param array $options
     * @return array
     */
    public function token($scene, $filename, array $options = [])
    {
        return $this->uploader($scene)->token($scene, $filename, $options);
    }

    /**
     * @inheritDoc
     */
    public function uploader($scene = null)
    {
        $scene = $this->getSceneAlias($scene);

        return $this->driver($scene);
    }

    /**
     * 获取场景别名
     * @param string $scene
     * @return string
     */
    protected function getSceneAlias($scene)
    {
        if (!$scene) {
            return $scene;
        }

        return $this->getConfig('aliases.' . $scene, $scene);
    }

    /**
     * 获取提供者
     * @param string $scene
     * @return \App\Contracts\Uploader\DataProvider
     */
    public function getProvider($scene)
    {
        $scene = $this->getSceneAlias($scene);
        $providerClass = $this->getDriverConfig($scene . 'provider');

        if (empty($providerClass)) {
            $providerClass = $this->getDefaultConfig('provider');
        }

        if (empty($providerClass)) {
            throw new \RuntimeException("UploadManager scene({$scene}) provider not defined.");
        }

        return app($providerClass);
    }

    /**
     * 创建默认驱动
     * @param string $name
     * @param array $config
     * @return \App\Services\Uploader\QiniuUploader
     */
    protected function createDefaultDriver($name, $config)
    {
        $defaultDisk = $this->getDefaultConfig('disk', 'default');
        $disk = Arr::get($config, 'disk', $defaultDisk);

        return new QiniuUploader($this->filesystemManager->disk($disk), $config);
    }

    /**
     * 获取默认驱动
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->getConfig('defaults.scene', 'default');
    }

    /**
     * 设置默认驱动
     * @param string $name
     */
    public function setDefaultDriver($name)
    {
        $this->setConfig('defaults.scene', $name);
    }

    /**
     * 获取驱动配置
     * @param string $name
     * @return array|\ArrayAccess|mixed
     */
    public function getDriverConfig($name)
    {
        $key = 'scenes';

        return $this->getConfig($name ? "{$key}.{$name}" : $key);
    }

    /**
     * @param string $key
     * @param mixed $default
     * @return array|\ArrayAccess|mixed
     */
    protected function getDefaultConfig($key, $default = null)
    {
        return $this->getConfig('defaults.' . $key, $default);
    }
}
