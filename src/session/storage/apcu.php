<?php

namespace nightmare\session\storage;

use SessionHandlerInterface;
use ReturnTypeWillChange;

class apcu implements SessionHandlerInterface
{
    private string $prefix;
    private int $ttl;

    public function __construct(string $prefix = 'sess_', int $ttl = 86400)
    {
        $this->prefix = $prefix;
        $this->ttl = $ttl;
    }

    #[ReturnTypeWillChange]
    public function close()
    {
        return true;
    }
    
    #[ReturnTypeWillChange]
    public function destroy($id)
    {
        $key = $this->prefix . $id; 
        return apcu_delete($key);
    }

    #[ReturnTypeWillChange]
    public function gc($max_lifetime)
    {
        return 1;
    }
    
    #[ReturnTypeWillChange]
    public function open($path, $name)
    {
        return true;
    }

    #[ReturnTypeWillChange]
    public function read($id)
    {
        $key = $this->prefix . $id; 
        return apcu_exists($key) ? apcu_fetch($key) : '';
    }
    
    #[ReturnTypeWillChange]
    public function write($id, $data)
    {
        $key = $this->prefix . $id; 
        return apcu_store($key, $data, $this->ttl);
    }
}