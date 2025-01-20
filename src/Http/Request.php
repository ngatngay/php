<?php

namespace NgatNgay\Http;

class Request
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

        $this->header = $this->initHeader();

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

        $this->initFile();
    }

    private function initHeader()
    {
        $headers = [];

        foreach ($this->server as $name => $value) {
            if (substr($name, 0, 5) == 'http_') {
                $headers[str_replace('_', '-', substr($name, 5))] = $value;
            }
        }

        return $headers;
    }

    private function initFile()
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

    public function isCLI(): bool
    {
        return php_sapi_name() === 'cli';
    }
    public function isCLIServer(): bool
    {
        return php_sapi_name() === 'cli-server';
    }

    public function getScriptName(): string
    {
        return $this->server('script_name');
    }


    public function getMethod()
    {
        return strtolower($this->server['request_method'] ?? 'get');
    }

    public function isMethod(string $value)
    {
        return strtolower($value) === $this->getMethod();
    }

    public function getClientIp()
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

    public function getUserAgent()
    {
        return $this->header['user-agent'] ?? '';
    }

    public function getReferer()
    {
        return $this->header['referer'] ?? '';
    }

    public function getHost()
    {
        return $this->server['server_name'];
    }
    public function getBaseUrl()
    {
        return ($this->server['request_scheme'] ?? 'http')
            . '://'
            . ($this->server['server_name'] ?? 'localhost');
    }

    public function getUri(string $mode = 'full')
    {
        $uri = $this->server['request_uri'];

        switch ($mode) {
            case 'request':
                return $uri;
            case 'no_query':
                return strtok($uri, '?');
            default:
                return $this->getBaseUrl() . $uri;
        }
    }

    public function get($key, $default = null)
    {
        return $this->get[$key] ?? $default;
    }

    public function hasGet($key)
    {
        return isset($this->get[$key]);
    }

    public function post($key, $default = null)
    {
        return $this->post[$key] ?? $default;
    }

    public function hasPost($key)
    {
        return isset($this->post[$key]);
    }

    public function header($key, $default = null)
    {
        return $this->header[$key] ?? $default;
    }
    public function hasHeader($key)
    {
        return isset($this->header[$key]);
    }

    public function cookie($key, $default = null)
    {
        return $this->cookie[$key] ?? $default;
    }
    public function hasCookie($key)
    {
        return isset($this->cookie[$key]);
    }

    public function sessionStart(string $prefix = 'sess_', int $ttl = 86400)
    {
        session_set_save_handler(new \NgatNgay\Session\Storage\Apcu($prefix, $ttl));

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
    public function hasSession($key)
    {
        return isset($this->session[$key]);
    }
    public function setSession($key, $value)
    {
        $_SESSION[$key] = $value;
    }
    public function removeSession($key)
    {
        unset($_SESSION[$key]);
    }

    public function server($key, $default = null)
    {
        return $this->server[$key] ?? $default;
    }
    public function hasServer($key)
    {
        return isset($this->server[$key]);
    }

    public function request($key, $default = null)
    {
        return $this->request[$key] ?? $default;
    }
    public function hasRequest($key)
    {
        return isset($this->request[$key]);
    }

    public function payload($key, $default = null)
    {
        return $this->payload[$key] ?? $default;
    }
    public function hasPayload($key)
    {
        return isset($this->payload[$key]);
    }
}
