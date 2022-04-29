<?php

namespace App\Support;

use Illuminate\Support\Collection;

class ArrUtil
{
    /**
     * @param \Illuminate\Support\Collection $collect
     * @param $sorts
     * @return \Illuminate\Support\Collection
     */
    public static function sort(Collection $collect, $sorts)
    {
        return $collect->sort(function ($it1, $it2) use ($sorts) {
            foreach ($sorts as $field => $type) {
                $isDesc = strtolower($type) == 'desc';
                if ($it1[$field] > $it2[$field]) {
                    return $isDesc ? -1 : 1;
                } elseif ($it1[$field] < $it2[$field]) {
                    return $isDesc ? 1 : -1;
                }
            }

            return 0;
        })->values();
    }
}