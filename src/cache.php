<?php

namespace ngatngay;

class cache
{
    private static bool $debug = false;
    private static ?int $expire = null;
    private static string $prefix = '';

    private static mixed $adapter;
    private static array $adapters = [];

    // common

    public static function set_debug(bool $debug): void
    {
        self::$debug = $debug;
    }

    public static function set_prefix(string $prefix): void
    {
        self::$prefix = $prefix;
    }

    public static function set_expire(?int $ttl = null): void
    {
        self::$expire = $ttl;
    }

    // adapter

    public static function set_adapter(string $key): void
    {
        self::$adapter = self::$adapters[$key];
    }

    public static function get_adapter(string $key)
    {
        return self::$adapters[$key];
    }

    public static function add_adapter(string $key, mixed $adapter): void
    {
        self::$adapters = array_merge(self::$adapters, [$key => $adapter]);
    }

    public function remove_adapter(string $key): void
    {
        unset(self::$adapters[$key]);
    }

    public static function get_adapters(): array
    {
        return self::$adapters;
    }

    // cache

    public static function has(string $key): bool
    {
        return self::$adapter->hasItem(self::$prefix . $key);
    }

    // truyen 1 tham so - lay binh thuong
    // truyen 2 tham so tro len - luu cache cho lan sau
    public static function get(string $key, mixed $default = null, array $opt = [])
    {
        $opt += [
            'expire' => self::$expire,
            'debug' => false,
            'save' => true,
            'save_if' => null, // ?callable
        ];

        if (self::$debug || $opt['debug']) {
            self::unset(self::$prefix . $key);
        }

        // get cache
        $item = self::$adapter->getItem(self::$prefix . $key);

        if ($item->isHit()) {
            return $item->get();
        } else {
            if (is_callable($default)) {
                $default = call_user_func($default, $opt);
            }

            if ($opt['save']) {
                $save = false;

                if (is_callable($opt['save_if'])) {
                    if (call_user_func($opt['save_if'], $default)) {
                        $save = true;
                    }
                } else {
                    $save = true;
                }

                if ($save) {
                    self::set($key, $default, $opt['expire']);
                }
            }

            return $default;
        }
    }

    public static function set(string $key, mixed $value, ?int $expire = null)
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

    public static function unset(string $key): bool
    {
        return self::$adapter->deleteItem(self::$prefix . $key);
    }

    public static function clear(string $prefix = ''): bool
    {
        return self::$adapter->clear($prefix);
    }
}
