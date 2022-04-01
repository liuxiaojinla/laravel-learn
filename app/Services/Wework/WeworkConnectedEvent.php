<?php

namespace App\Services\Wework;

class WeworkConnectedEvent
{
    protected $clientId;

    /**
     * @param string $clientId
     */
    public function __construct($clientId)
    {
        $this->clientId = $clientId;
    }

    /**
     * @return string
     */
    public function getClientId()
    {
        return $this->clientId;
    }
}