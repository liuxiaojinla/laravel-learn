<?php

namespace App\Models\Wechat;

class Util
{
    /**
     * @param array|string $value
     * @return string
     */
    public static function transformStrList($value)
    {
        if (is_string($value)) {
            $value = explode(',', $value);
        } elseif (!is_array($value)) {
            $value = [$value];
        }

        $value = array_filter($value, function ($item) {
            if ($item === '' || $item === null || is_bool($item)) {
                return false;
            }

            return true;
        });

        return implode(',', $value);
    }
}
