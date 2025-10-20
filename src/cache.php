<?php

namespace ngatngay;

class cache
{
    /**
     * @var bool
     */
    private static $debug = false;

    /**
     * @var int|null
     */
    private static $expire = null;

    /**
     * @var string
     */
    private static $prefix = '';

    /**
     * @var mixed
     */
    private static $adapter;

    /**
     * @var array
     */
    private static $adapters = [];

    // common

    /**
     * @param bool $debug
     * @return void
     */
    public static function set_debug($debug)
    {
        self::$debug = $debug;
    }

    /**
     * @param string $prefix
     * @return void
     */
    public static function set_prefix($prefix)
    {
        self::$prefix = $prefix;
    }

    /**
     * @param int|null $ttl
     * @return void
     */
    public static function set_expire($ttl = null)
    {
        self::$expire = $ttl;
    }

    // adapter

    /**
     * @param string $key
     * @return void
     */
    public static function set_adapter($key)
    {
        self::$adapter = self::$adapters[$key];
    }

    /**
     * @param string $key
     * @return mixed
     */
    public static function get_adapter($key)
    {
        return self::$adapters[$key];
    }

    /**
     * @param string $key
     * @param mixed $adapter
     * @return void
     */
    public static function add_adapter($key, $adapter)
    {
        self::$adapters = array_merge(self::$adapters, [$key => $adapter]);
    }

    /**
     * @param string $key
     * @return void
     */
    public function remove_adapter($key)
    {
        unset(self::$adapters[$key]);
    }

    /**
     * @return array
     */
    public static function get_adapters()
    {
        return self::$adapters;
    }

    // cache

    /**
     * @param string $key
     * @return bool
     */
    public static function has($key)
    {
        return self::$adapter->hasItem(self::$prefix . $key);
    }

    // truyen 1 tham so - lay binh thuong
    // truyen 2 tham so tro len - luu cache cho lan sau
    /**
     * @param string $key
     * @param mixed $default
     * @param array $opt
     * @return mixed
     */
    public static function get($key, $default = null, $opt = [])
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

    /**
     * @param string $key
     * @param mixed $value
     * @param int|null $expire
     * @return bool
     */
    public static function set($key, $value, $expire = null)
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

    /**
     * @param string $key
     * @return bool
     */
    public static function unset($key)
    {
        return self::$adapter->deleteItem(self::$prefix . $key);
    }

    /**
     * @param string $prefix
     * @return bool
     */
    public static function clear($prefix = '')
    {
        return self::$adapter->clear($prefix);
    }
}
