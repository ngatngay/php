<?php

namespace ngatngay;

class config
{
    private $config;
    private $file;
    private $data = [];
    private $prefix = '';

    public function __construct($config)
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

    public function set_prefix(string $prefix)
    {
        $this->prefix = $prefix;
    }

    public function get(string $key, $default = null)
    {
        if (file_exists($this->config['file'])) {
            $this->data = require $this->config['file'];
        }

        return $this->data[$this->prefix . $key] ?? $default;
    }

    public function set(string $key, $value)
    {
        $this->data[$this->prefix . $key] = $value;

        file_put_contents($this->config['file'], '<?php return ' . var_export($this->data, true) . ';');
    }
    
    public function remove(string $key)
    {
        unset($this->data[$this->prefix . $key]);

        file_put_contents($this->config['file'], '<?php return ' . var_export($this->data, true) . ';');
    }
}
