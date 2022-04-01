<?php

namespace App\Support;

class GeoUtil
{
    /**
     * 计算两点之间的距离
     * @param float $lng1 经度1
     * @param float $lat1 纬度1
     * @param float $lng2 经度2
     * @param float $lat2 纬度2
     * @param int $decimal 位数
     * @return float
     */
    public static function getDistance($lng1, $lat1, $lng2, $lat2, $decimal = 2)
    {
        $EARTH_RADIUS = 6370.996; // 地球半径系数
        $PI = 3.1415926535898;

        $radLat1 = $lat1 * $PI / 180.0;
        $radLat2 = $lat2 * $PI / 180.0;

        $radLng1 = $lng1 * $PI / 180.0;
        $radLng2 = $lng2 * $PI / 180.0;

        $a = $radLat1 - $radLat2;
        $b = $radLng1 - $radLng2;

        $distance = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2)));
        $distance = $distance * $EARTH_RADIUS * 1000;

        return round($distance, $decimal);
    }

    /**
     * 计算两点之间的距离
     * @param float $lng1 经度1
     * @param float $lat1 纬度1
     * @param float $lng2 经度2
     * @param float $lat2 纬度2
     * @param float $range 范围
     * @return bool
     */
    public static function hasRange($lng1, $lat1, $lng2, $lat2, $range)
    {
        $distance = static::getDistance($lng1, $lat1, $lng2, $lat2);

        return $distance <= $range;
    }
}
