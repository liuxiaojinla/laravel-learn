<?php

namespace App\Services\Wechat\Work\Client;

class ContactWayClient extends \EasyWeChat\Work\ExternalContact\ContactWayClient
{
    /**
     * 获取企业已配置的「联系我」方式.
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function list(array $params = [])
    {
        return $this->httpPostJson('cgi-bin/externalcontact/list_contact_way', $params);
    }
}
