<?php

namespace App\Services\Limiter;

class RegionLimiter extends AbstractLimiter
{
    // 白名单
    const TYPE_ONLY = 0;

    // 黑名单
    const TYPE_GUARD = 1;

    /**
     * @inheritDoc
     */
    protected function check($data)
    {
        $cityCode = $data['city_code'];
        $limitSource = $this->config['source'];
        $limitType = $this->config['type'] ?? 0;

        $limitCities = $this->readCities($limitSource);
        if ($limitType == self::TYPE_ONLY) {
            if (!in_array($cityCode, $limitCities)) {
                throw new LimitException();
            }
        } else {
            if (!in_array($cityCode, $limitCities)) {
                throw new LimitException();
            }
        }
    }

    protected function readCities($limitSource)
    {
        return [];
    }
}
