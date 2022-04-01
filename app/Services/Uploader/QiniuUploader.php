<?php

namespace App\Services\Uploader;

use Illuminate\Support\Arr;

class QiniuUploader extends AbstractUploader
{

    /**
     * @inheritDoc
     */
    public function file($scene, \SplFileInfo $file, array $options = [])
    {
        $options = $this->optimizeOptions($options);

        $key = $this->key($scene, $file->getFilename(), $options);
        $this->filesystem->put($key, file_get_contents($file->getRealPath()));
        $config = $this->filesystem->getDriver()->getConfig();
        $url = $this->carryUrl($config);

        return [
            'key' => $key,
            'path' => $key,
            'url' => $this->concatPathToUrl($url, $key),
            'filename' => $file->getFilename(),
            'size' => $file->getSize(),
            'extension' => $file->getExtension(),
            'mime' => mime_content_type($file->getRealPath()),
            'md5' => md5_file($file->getRealPath()),
            'sha1' => sha1_file($file->getRealPath()),
        ];
    }

    /**
     * @inheritDoc
     */
    public function token($scene, $filename, array $options = [])
    {
        $key = $this->key($scene, $filename, $options);

        $policy = $this->policy($scene, $filename, $options);

        $expires = $this->expire($options);

        $token = $this->filesystem->getUploadToken(
            $key, $expires, $policy, true
        );

        return [
            'key' => $key,
            'token' => $token,
            'policy' => $policy,
        ];
    }

    /**
     * @param string $scene
     * @param string $filename
     * @param array $options
     * @return string
     */
    protected function key($scene, $filename, array $options)
    {
        $basePath = $options['base_path'];

        return "{$basePath}/{$scene}/{$filename}";
    }

    /**
     * @param string $scene
     * @param string $filename
     * @param array $options
     * @return array
     */
    protected function policy($scene, $filename, array $options)
    {
        $policy = [
            'callbackUrl' => $this->callbackUrl($scene, $options),
            'callbackBody' => $this->callbackBody($scene, $options),
            'callbackBodyType' => 'application/json',
        ];

        $size = Arr::get($options, 'size');
        if ($size) {
            $policy['fsizeLimit'] = $size;
        }

        if (isset($config['mime'])) {
            $policy['mimeLimit'] = $config['mime'];
        }

        return $policy;
    }


    /**
     * @param string $scene
     * @param array $options
     * @return string
     */
    protected function callbackUrl($scene, array $options)
    {
        return Arr::get($options, 'callback_url', '');
    }

    /**
     * @param string $scene
     * @param array $options
     * @return string
     */
    protected function callbackBody($scene, array $options)
    {
        $cdn = $options['cdn'];
        $userData = $options['user_data'];

        $data = array_merge([
            'type' => $scene,
            'scene' => $scene,
            'url' => "{$cdn}/$(key)",
            'key' => '$(key)',
            'hash' => '$(etag)',
            'size' => '$(fsize)',
            'sha1' => '$(bodySha1)',
            'mime' => '$(mimeType)',
        ], $userData);

        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * @param array $options
     * @return int
     */
    protected function expire(array $options)
    {
        return Arr::get($options, 'expire', 300);
    }
}