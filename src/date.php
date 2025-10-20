<?php

namespace ngatngay;

class date
{
    /**
     * @return int
     */
    public static function now()
    {
        return time();
    }

    /**
     * @param int $day
     * @return int
     */
    public static function start_day($day)
    {
        return mktime(00, 00, 00, (int) date('n'), $day);
    }

    /**
     * @param int $month
     * @return int
     */
    public static function start_month($month)
    {
        return mktime(00, 00, 00, $month);
    }

    /**
     * @return int
     */
    public static function start_year()
    {
        return 0;
    }

    /**
     * @return string
     */
    public static function current_day()
    {
        return date('d');
    }

    /**
     * @return string
     */
    public static function current_month()
    {
        return date('m');
    }

    /**
     * @return string
     */
    public static function current_year()
    {
        return date('Y');
    }

    /**
     * @param int $time
     * @return string
     */
    public static function display_ago($time)
    {
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
