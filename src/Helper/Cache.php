<?php

namespace NgatNgay\Helper;

use Symfony\Component\Cache\Adapter\ApcuAdapter;
use Symfony\Component\Cache\Adapter\ChainAdapter;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\Adapter\PdoAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\Request;
use Psr\Container\ContainerInterface;
use Illuminate\Database\Capsule\Manager as Capsule;
use Symfony\Component\Cache\Psr16Cache;

class Cache
{
    private static $adapter;
    private static array $adapters = [];
    private static ?int $expire = null;
    private static string $prefix = '';
    
    public static function setPrefix($prefix) {
        self::$prefix = $prefix;
    }

    public static function setDefaultAdapter($key)
    {
        self::$adapter = self::$adapters[$key];
    }
    
    public static function getAdaper($key)
    {
        return self::$adapters[$key];
    }
    
    public static function addAdapter($key, $adapter)
    {
        self::$adapters = array_merge(self::$adapters, [$key => $adapter]);
    }

    public function removeAdapter($key) {
        unset(self::$adapters[$key]);
    }
    
    public static function getAdapters()
    {
        return self::$adapters;
    }

    public static function has($key)
    {
        return self::$adapter->hasItem(self::$prefix . $key);
    }

    // truyen 1 tham so - lay binh thuong
    // truyen 2 tham so tro len - luu cache cho lan sau
    public static function get($key, ?callable $default = null, $expire = null)
    {
        $item = self::$adapter->getItem(self::$prefix . $key);
        
        if (!$item->isHit() && is_callable($default)) {
            $value = call_user_func($default);
            self::set($key, $value, $expire);         
            return $value;
        }

        return $item->get();
    }

    public static function set($key, $value, $expire = null)
    {
        $item = self::$adapter->getItem(self::$prefix . $key);

        if ($expire !== null) {
            $item->expiresAfter($expire);
        } else if (self::$expire !== null) {
            $item->expiresAfter(self::$expire);
        }

        $item->set($value);
        return self::$adapter->save($item);
    }

    public static function remove($key)
    {
        $key = self::$prefix . $key;
        return self::$adapter->deleteItem($key);
    }
    
    public static function setDefaultExpire($ttl = null)
    {
        self::$expire = $ttl;
    }
}
