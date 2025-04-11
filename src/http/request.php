<?php

namespace ngatngay\http;

class request
{
    public array $get;
    public array $post;
    public array $file;
    public array $header;
    public array $cookie;
    public array $session;
    public array $server;
    public array $request;
    public array $payload;

    public function __construct()
    {
        $this->server = array_change_key_case($_SERVER);
        ksort($this->server);

        $this->header = $this->init_header();

        /*
        {
    $request_uri = @parse_url($_SERVER['REQUEST_URI'] ?? '');
    $add_get = [];

    if (isset($request_uri['query'])) {
        parse_str($request_uri['query'], $add_get);
    }

    $_GET = array_merge($_GET, $add_get);
    $_REQUEST = array_merge($_REQUEST, $add_get);
}
*/

        $this->get = &$_GET;
        $this->post = &$_POST;
        $this->file = $_FILES;
        $this->cookie = &$_COOKIE;
        $this->request = &$_REQUEST;

        $data = @json_decode(file_get_contents('php://input'), true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $this->payload = $data;
        }

        $this->init_file();
    }

    private function init_header()
    {
        $headers = [];

        foreach ($this->server as $name => $value) {
            if (substr($name, 0, 5) == 'http_') {
                $headers[str_replace('_', '-', substr($name, 5))] = $value;
            }
        }

        return $headers;
    }

    private function init_file()
    {
        if (empty($this->file)) {
            return;
        }
        
        $tmp = [];
        foreach ($this->file as $key => $value) {
            if (!is_array($this->file[$key]['name'])) {
                $tmp[$key] = [$this->file[$key]];
                continue;
             }
             
             $fCount = count($this->file[$key]['name']);
             $fKeys = array_keys($this->file[$key]);
             
             for($i = 0; $i < $fCount; $i++) {
                 foreach ($fKeys as $fKey) {
                     $tmp[$key][$i][$fKey] = $this->file[$key][$fKey][$i];
                 }
             }
        }
        
        $this->file = $tmp;
    }

    public function is_cli(): bool
    {
        return php_sapi_name() === 'cli';
    }
    public function is_cli_erver(): bool
    {
        return php_sapi_name() === 'cli-server';
    }

    public function get_script_name(): string
    {
        return $this->server('script_name');
    }


    public function get_method()
    {
        return strtolower($this->server['request_method'] ?? 'get');
    }

    public function is_method(string $value)
    {
        return strtolower($value) === $this->get_method();
    }

    public function get_client_ip()
    {
        $keys = [
            'http_client_ip',
            'http_x_forwarded_for',
            'http_x_forwarded',
            'http_forwarded_for',
            'http_forwarded',
            'remote_addr'
        ];
        foreach ($keys as $key) {
            if (isset($this->server[$key])) {
                return $this->server[$key];
            }
        }

        return '127.0.0.1';
    }

    public function get_user_agent()
    {
        return $this->header['user-agent'] ?? '';
    }

    public function get_referer()
    {
        return $this->header['referer'] ?? '';
    }

    public function get_host()
    {
        return $this->header('host');
    }
    public function get_base_url()
    {
        return ($this->server['request_scheme'] ?? 'http')
            . '://'
            . ($this->server['server_name'] ?? 'localhost');
    }

    public function get_uri(string $mode = 'full')
    {
        $uri = $this->server['request_uri'];

        switch ($mode) {
            case 'request':
                return $uri;
            case 'no_query':
                return strtok($uri, '?');
            default:
                return $this->get_base_url() . $uri;
        }
    }
    
    public function query_string() {
        return (string) $this->server('query_string');
    }

    public function get($key, $default = null)
    {
        return $this->get[$key] ?? $default;
    }

    public function has_get($key)
    {
        return isset($this->get[$key]);
    }

    public function post($key, $default = null)
    {
        return $this->post[$key] ?? $default;
    }

    public function has_post($key)
    {
        return isset($this->post[$key]);
    }

    public function header($key, $default = null)
    {
        return $this->header[$key] ?? $default;
    }
    public function has_header($key)
    {
        return isset($this->header[$key]);
    }

    public function cookie($key, $default = null)
    {
        return $this->cookie[$key] ?? $default;
    }
    public function has_cookie($key)
    {
        return isset($this->cookie[$key]);
    }

    public function session_start(string $prefix = 'sess_', int $ttl = 86400)
    {
        session_set_save_handler(new \ngatngay\session\storage\apcu($prefix, $ttl));

        if (PHP_SESSION_ACTIVE === session_status()) {
            throw new \RuntimeException('Failed to start the session: already started by PHP.');
        }

        if (!\session_start()) {
            throw new \RuntimeException('Failed to start the session.');
        }
        $this->session = &$_SESSION;
    }
    public function session($key, $default = null)
    {
        return $this->session[$key] ?? $default;
    }
    public function has_session($key)
    {
        return isset($this->session[$key]);
    }
    public function set_session($key, $value)
    {
        $_SESSION[$key] = $value;
    }
    public function remove_session($key)
    {
        unset($_SESSION[$key]);
    }

    public function server($key, $default = null)
    {
        return $this->server[$key] ?? $default;
    }
    public function has_server($key)
    {
        return isset($this->server[$key]);
    }

    public function request($key, $default = null)
    {
        return $this->request[$key] ?? $default;
    }
    public function has_request($key)
    {
        return isset($this->request[$key]);
    }

    public function payload($key, $default = null)
    {
        return $this->payload[$key] ?? $default;
    }
    public function has_payload($key)
    {
        return isset($this->payload[$key]);
    }
}
