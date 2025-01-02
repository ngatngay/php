<?php

namespace NgatNgay\Helper;

class Date
{
    public static function now()
    {
        return time();
    }

    public static function startDay($day)
    {
        return mktime(00, 00, 00, date('n'), $day);
    }

    public static function startMonth($month)
    {
        return mktime(00, 00, 00, $month);
    }

    public static function startYear()
    {
    }


    public static function currentDay()
    {
        return date('d');
    }

    public static function currentMonth()
    {
        return date('m');
    }

    public static function currentYear()
    {
        date('Y');
    }

    public static function displayAgo($time)
    {
        $time  = intval($time);
        $times = time() - $time;

        if ($times < 1) {
            $t = 'Vừa xong';
        } elseif ($times < 60) {
            $t = $times . ' giây trước';
        } elseif ($times < 3600) {
            $t = round($times / 60) . ' phút trước';
        } elseif ($times < 86400) {
            $t = round($times / 3600) . ' giờ trước';
        } elseif ($times < 2_592_000) {
            $t = round($times / 86400) . ' ngày trước';
        } elseif ($times < 31_536_000) {
            $t = round($times / 2_592_000) . ' tháng trước';
        } else {
            $t = round($times / 31_536_000) . ' năm trước';
        }

        return $t;
    }
}
