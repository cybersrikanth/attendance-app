<?php

namespace App\Helpers;

class TimeHelper
{
    static function isFuture($date)
    {
        return $date > strtotime(date('Y-m-d'));
    }

    static function formatedDate($timestamp)
    {
        return date("Y-m-d", $timestamp);
    }
}
