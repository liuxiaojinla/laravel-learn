<?php

namespace App\Services\Wework;


class WeworkReceiveEvent
{
    /**
     * @var string
     */
    protected $clientId;

    /**
     * @var string
     */
    protected $data;

    /**
     * @var int
     */
    protected $length;

    /**
     * @param string $clientId
     * @param string $data
     * @param int $length
     */
    public function __construct($clientId, $data, $length)
    {
        $this->clientId = $clientId;
        $this->data = $data;
        $this->length = $length;
    }

    /**
     * @return string
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return int
     */
    public function getLength()
    {
        return $this->length;
    }
}