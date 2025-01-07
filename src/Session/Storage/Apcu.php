<?php

namespace NgatNgay\Session\Storage;

class Apcu implements \SessionHandlerInterface
{
    private string $prefix;
    private int $ttl;

    public function __construct(string $prefix = 'sess_', int $ttl = 86400)
    {
        $this->prefix = $prefix;
        $this->ttl = $ttl;
    }

    public function close(): bool
    {
        return true;
    }
    
    public function destroy(string $id): bool
    {
        $key = $this->prefix . $id; 
        return apcu_delete($key);
    }

    public function gc(int $max_lifetime): int|false
    {
        return true;
    }
    
    public function open(string $path, string $name): bool
    {
        return true;
    }

    public function read(string $id): string | false
    {
        $key = $this->prefix . $id; 
        return apcu_exists($key) ? apcu_fetch($key) : '';
    }
    
    public function write(string $id, string $data): bool
    {
        $key = $this->prefix . $id; 
        return apcu_store($key, $data, $this->ttl);
    }
}