<?php

namespace App\Contracts\Wechat;

interface Factory
{
    /**
     * @return \EasyWeChat\MiniProgram\Application
     */
    public function miniProgram(): \EasyWeChat\MiniProgram\Application;

    /**
     * @return boolean
     */
    public function hasMiniProgram(): bool;

    /**
     * @return \EasyWeChat\OfficialAccount\Application
     */
    public function official(): \EasyWeChat\OfficialAccount\Application;

    /**
     * @return boolean
     */
    public function hasOfficial();

    /**
     * @return \EasyWeChat\OpenPlatform\Application
     */
    public function openPlatform(): \EasyWeChat\OpenPlatform\Application;

    /**
     * @return boolean
     */
    public function hasOpenPlatform();

    /**
     * @param string $name
     * @return \EasyWeChat\Work\Application
     */
    public function work($name = null): \EasyWeChat\Work\Application;

    /**
     * @return boolean
     */
    public function hasWork(): bool;

    /**
     * @return \EasyWeChat\OpenWork\Application
     */
    public function openWork(): \EasyWeChat\OpenWork\Application;

    /**
     * @return boolean
     */
    public function hasOpenWork(): bool;
}
