<?php

namespace App\Http\Controllers\Notify;

use App\Contracts\Wechat\Factory as WechatFactory;

class WechatOfficialController
{
    /**
     * @var WechatFactory
     */
    private $wf;

    /**
     * @param WechatFactory $wf
     */
    public function __construct(WechatFactory $wf)
    {
        $this->wf = $wf;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \EasyWeChat\Kernel\Exceptions\BadRequestException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function __invoke()
    {
        $response = $this->wf->official()->server->serve();

        return $response;
    }

}