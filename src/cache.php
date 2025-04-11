<?php

namespace ngatngay;

class cache
{
    private static bool $debug = false;
    private static ?int $expire = null;
    private static string $prefix = '';

    private static $adapter;
    private static array $adapters = [];

    // common

    public static function set_debug(bool $debug)
    {
        self::$debug = $debug;
    }

    public static function set_prefix(string $prefix)
    {
        self::$prefix = $prefix;
    }

    public static function set_expire(?int $ttl = null)
    {
        self::$expire = $ttl;
    }

    // adapter

    public static function set_adapter(string $key)
    {
        self::$adapter = self::$adapters[$key];
    }

    public static function get_adapter(string $key)
    {
        return self::$adapters[$key];
    }

    public static function add_adapter(string $key, $adapter)
    {
        self::$adapters = array_merge(self::$adapters, [$key => $adapter]);
    }

    public function remove_adapter(string $key)
    {
        unset(self::$adapters[$key]);
    }

    public static function get_adapters(): array
    {
        return self::$adapters;
    }

    // cache

    public static function has(string $key)
    {
        return self::$adapter->hasItem(self::$prefix . $key);
    }

    // truyen 1 tham so - lay binh thuong
    // truyen 2 tham so tro len - luu cache cho lan sau
    public static function get(string $key, $default = null, array $opt = [])
    {
        $opt += [
            'empty' => true,
            'expire' => self::$expire,
            'refresh' => false,
        ];

        if (self::$debug || $opt['refresh']) {
            self::remove(self::$prefix . $key);
        }

        $item = self::$adapter->getItem(self::$prefix . $key);

        if ($item->isHit()) {
            return $item->get();
        } else {
            if (is_callable($default)) {
                $default = call_user_func($default, $opt);
            }

            if ($opt['empty'] || (!$opt['empty'] && !empty($default))) {
                self::set($key, $default, $opt['expire']);
            }

            return $default;
        }
    }

    public static function set(string $key, $value, ?int $expire = null)
    {
        $item = self::$adapter->getItem(self::$prefix . $key);

        if ($expire !== null) {
            $item->expiresAfter($expire);
        } elseif (self::$expire !== null) {
            $item->expiresAfter(self::$expire);
        }

        $item->set($value);
        return self::$adapter->save($item);
    }

    public static function remove(string $key)
    {
        return self::$adapter->deleteItem(self::$prefix . $key);
    }

    public static function clear(string $prefix = '')
    {
        return self::$adapter->clear($prefix);
    }
}
