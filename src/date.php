<?php

namespace ngatngay;

class date
{
    public static function now(): int
    {
        return time();
    }

    public static function start_day(int $day): int
    {
        return mktime(00, 00, 00, (int) date('n'), $day);
    }

    public static function start_month(int $month): int
    {
        return mktime(00, 00, 00, $month);
    }

    public static function start_year(): int
    {
        return 0;
    }

    public static function current_day(): string
    {
        return date('d');
    }

    public static function current_month(): string
    {
        return date('m');
    }

    public static function current_year(): string
    {
        return date('Y');
    }

    public static function display_ago(int $time): string
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
