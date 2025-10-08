<?php

namespace ngatngay;

class config
{
    private array $config = [];
    private string $file;
    private array $data = [];
    private string $prefix = '';

    public function __construct(array $config)
    {
        $this->config = array_merge([
            'driver' => 'memory'
        ], $config);

        switch ($this->config['driver']) {
            case 'memory':
                break;
            case 'php_file':
                break;
        }
    }

    public function set_prefix(string $prefix): void
    {
        $this->prefix = $prefix;
    }

    public function get(string $key, mixed $default = null)
    {
        if (file_exists($this->config['file'])) {
            $this->data = require $this->config['file'];
        }

        return $this->data[$this->prefix . $key] ?? $default;
    }

    public function set(string $key, mixed $value): void
    {
        $this->data[$this->prefix . $key] = $value;

        file_put_contents($this->config['file'], '<?php return ' . var_export($this->data, true) . ';');
    }
    
    public function remove(string $key): void
    {
        unset($this->data[$this->prefix . $key]);

        file_put_contents($this->config['file'], '<?php return ' . var_export($this->data, true) . ';');
    }
}
