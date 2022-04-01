<?php

namespace App\Services\Wechat\Official\Events;

class UserLocation extends Event
{
    /**
     * @return float
     */
    public function getLatitude()
    {
        return $this->getMessageAttribute('Latitude', 0);
    }

    /**
     * @return float
     */
    public function getLongitude()
    {
        return $this->getMessageAttribute('Longitude', 0);
    }

    /**
     * @return float
     */
    public function getPrecision()
    {
        return $this->getMessageAttribute('Precision', 0);
    }
}
