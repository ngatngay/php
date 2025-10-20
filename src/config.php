<?php

namespace ngatngay;

class config
{
    /**
     * @var array
     */
    private $config = [];

    /**
     * @var string
     */
    private $file;

    /**
     * @var array
     */
    private $data = [];

    /**
     * @var string
     */
    private $prefix = '';

    /**
     * @param array $config
     * @return void
     */
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

    /**
     * @param string $prefix
     * @return void
     */
    public function set_prefix($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if (file_exists($this->config['file'])) {
            $this->data = require $this->config['file'];
        }

        return $this->data[$this->prefix . $key] ?? $default;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set($key, $value)
    {
        $this->data[$this->prefix . $key] = $value;

        file_put_contents($this->config['file'], '<?php return ' . var_export($this->data, true) . ';');
    }
    
    /**
     * @param string $key
     * @return void
     */
    public function remove($key)
    {
        unset($this->data[$this->prefix . $key]);

        file_put_contents($this->config['file'], '<?php return ' . var_export($this->data, true) . ';');
    }
}
