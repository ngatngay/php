<?php

namespace Nightmare\Http;

use Nightmare\Json;
use RuntimeException;

class Request
{
    /**
     * @var array|null
     */
    public $file = null;

    /**
     * @var array|null
     */
    public $header = null;

    /**
     * @var array|null
     */
    public $server = null;

    /**
     * @var array|null
     */
    public $payload = null;

    /**
     * @return void
     */
    public function __construct()
    {
    }

    // common

    /**
     * @return bool
     */
    public function is_cli()
    {
        return php_sapi_name() === 'cli';
    }
    /**
     * @return bool
     */
    public function is_cli_server()
    {
        return php_sapi_name() === 'cli-server';
    }

    /**
     * @return string
     */
    public function script_name()
    {
        return $this->server('script_name');
    }

    /**
     * @return string
     */
    public function method()
    {
        return strtolower((string) $this->server('REQUEST_METHOD', 'get'));
    }

    /**
     * @param string $value
     * @return bool
     */
    public function is_method($value)
    {
        return strtolower($value) === $this->method();
    }
    
    /**
     * @return bool
     */
    public function is_ajax() {
        return $this->has_header('X_REQUESTED_WITH') &&
           strtolower((string) $this->header('X_REQUESTED_WITH')) === 'xmlhttprequest';
    }

    /**
     * @return string
     */
    public function ip()
    {
        $keys = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];
        foreach ($keys as $key) {
            if (isset($_SERVER[$key])) {
                return $_SERVER[$key];
            }
        }

        return '127.0.0.1';
    }

    /**
     * @return string
     */
    public function user_agent()
    {
        return (string) $this->header('user_agent');
    }

    /**
     * @return string
     */
    public function referer()
    {
        return (string) $this->header('referer');
    }

    /**
     * @return string
     */
    public function host()
    {
        return (string) $this->header('host');
    }
    /**
     * @return string
     */
    public function base_url()
    {
        return $this->server('request_scheme', 'http')
            . '://'
            . $this->server('server_name', 'localhost');
    }

    /**
     * @param string $mode
     * @return string
     */
    public function uri($mode = 'full')
    {
        $uri = $this->server('request_uri');

        switch ($mode) {
            case 'request':
                return $uri;
            case 'no_query':
                return strtok($uri, '?');
            default:
                return $this->base_url() . $uri;
        }
    }

    /**
     * @return string
     */
    public function query_string()
    {
        return (string) $this->server('query_string');
    }

    // HEADER
    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function header($key = '', $default = null)
    {
        if ($key === '') {
            $headers = [];
            foreach ($_SERVER as $key => $value) {
                if (str_starts_with($key, 'HTTP_')) {
                    $headers[str_replace('_', '-', strtolower(substr($key, 5)))] = $value;
                }
            }

            return $headers;
        }

        return $_SERVER['HTTP_' . str_replace('-', '_', strtoupper($key))] ?? $default;
    }
    /**
     * @param string $key
     * @return bool
     */
    public function has_header($key)
    {
        return isset($_SERVER['HTTP_' . strtoupper($key)]);
    }

    // QUERY

    public function query_all() {
        return $_GET;
    }

    public function query($key = '', $default = null)
    {
        if ($key === '') {
            return $_GET;
        }

        return $_GET[$key] ?? $default;
    }
    /**
     * @param string $key
     * @return bool
     */
    public function has_query($key)
    {
        return isset($_GET[$key]);
    }
    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set_query($key, $value)
    {
        $_GET[$key] = $value;
    }

    // POST

    public function post_all() {
        return $_POST;
    }

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function post($key, $default = null)
    {
        if ($key === '') {
            return $_POST;
        }

        return $_POST[$key] ?? $default;
    }
    /**
     * @param string $key
     * @return bool
     */
    public function has_post($key)
    {
        return isset($_POST[$key]);
    }
    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set_post($key, $value)
    {
        $_POST[$key] = $value;
    }

    // COOKIE

    public function cookie_all()
    {
        return $_COOKIE;
    }

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function cookie($key, $default = null)
    {
        if ($key === '') {
            return $_COOKIE;
        }

        return $_COOKIE[$key] ?? $default;
    }
    /**
     * @param string $key
     * @return bool
     */
    public function has_cookie($key)
    {
        return isset($_COOKIE[$key]);
    }
    /**
     * @param string $key
     * @param string $value
     * @return void
     */
    public function set_cookie($key, $value)
    {
        $_COOKIE[$key] = $value;
    }

    // SESSION
    
    public function session_all()
    {
        return $_SESSION;
    }

    /**
     * @param string $prefix
     * @param int $ttl
     * @return void
     */
    public function session_start($prefix = 'sess_', $ttl = 86400)
    {
        if (PHP_SESSION_ACTIVE === session_status()) {
            throw new RuntimeException('Failed to start the session: already started by PHP.');
        }

        if (!\session_start()) {
            throw new RuntimeException('Failed to start the session.');
        }
    }

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function session($key, $default = null)
    {
        if ($key === '') {
            return $_SESSION;
        }

        return $_SESSION[$key] ?? $default;
    }
    /**
     * @param string $key
     * @return bool
     */
    public function has_session($key)
    {
        return isset($_SESSION[$key]);
    }
    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set_session($key, $value)
    {
        $_SESSION[$key] = $value;
    }
    /**
     * @param string $key
     * @return void
     */
    public function unset_session($key)
    {
        unset($_SESSION[$key]);
    }


    // SERVER

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function server($key, $default = null)
    {
        if ($key === '') {
            return $_SERVER;
        }

        return (string) (isset($_SERVER[$key]) ? $_SERVER[$key] : (isset($_SERVER[strtoupper($key)]) ? $_SERVER[strtoupper($key)] : $default));
    }
    /**
     * @param string $key
     * @return bool
     */
    public function has_server($key)
    {
        return isset($_SERVER[$key]) ? true : isset($_SERVER[strtoupper($key)]);
    }

    // FILES
    /**
     * @param string $key
     * @return array|null
     */
    public function file($key)
    {
        if ($key === '') {
            return $_FILES;
        }

        if (!isset($_FILES[$key])) {
            return null;
        }

        if (!is_array($_FILES[$key]['name'])) {
            return [$_FILES[$key]];
        }

        $tmp = [];
        foreach ($_FILES[$key] as $k => $v) {
            $fCount = count($_FILES[$key]['name']);
            $fKeys = array_keys($_FILES[$key]);

            for ($i = 0; $i < $fCount; $i++) {
                foreach ($fKeys as $fKey) {
                    $tmp[$key][$i][$fKey] = $_FILES[$key][$fKey][$i];
                }
            }
        }
        return $tmp;
    }

    // REQUEST

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function request($key, $default = null)
    {
        if ($key === '') {
            return $_REQUEST;
        }

        return $_REQUEST[$key] ?? $default;
    }
    /**
     * @param string $key
     * @return bool
     */
    public function has_request($key)
    {
        return isset($_REQUEST[$key]);
    }

    // PAYLOAD

    /**
     * @return void
     */
    public function init_payload()
    {
        $this->payload = Json::decode(file_get_contents('php://input') ?: '[]', true);
    }
    /**
     * @param string $key
     * @return bool
     */
    public function has_payload($key)
    {
        return isset($this->payload[$key]);
    }
    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function payload($key = '', $default = null)
    {
        if ($key === '') {
            return $this->payload;
        }

        return $this->payload[$key] ?? $default;
    }
}
