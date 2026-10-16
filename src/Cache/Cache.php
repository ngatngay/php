<?php

/*
 * Wraper for Symfony Cache
 * use with Cache Adapter
 * https://symfony.com/doc/current/components/cache.html
 */

namespace Nightmare\Cache;

class Cache
{
    private $adapter;

    public function __construct($adapter) {
        $this->adapter = $adapter;
    }

    public function has($key)
    {
        return $this->adapter->hasItem($key);
    }

    public function get($key, $default = null)
    {
        $item = $this->adapter->getItem($key);
        return $item->isHit() ? $item->get() : $default;
    }

    public function set($key, $value, $ttl = null)
    {
        $item = $this->adapter->getItem($key);
        $item->expiresAfter($ttl);
        $item->set($value);

        return $this->adapter->save($item);
    }

    public function delete($key)
    {
        return $this->adapter->deleteItem($key);
    }

    public function clear()
    {
        return $this->adapter->clear();
    }
    
    public function r($key, $callback, $ttl = null)
    {
        $item = $this->adapter->getItem($key);

        if ($item->isHit()) {
            return $item->get();
        }

        $item->expiresAfter($ttl);
        $item->set($callback());
        $this->adapter->save($item);

        return $item->get();
    }
}
