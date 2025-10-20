<?php

namespace ngatngay\http;

use ngatngay\json;
use RuntimeException;

class request
{
    /**
     * @var array
     */
    public static $file;

    /**
     * @var array
     */
    public static $header;

    /**
     * @var array
     */
    public static $server;

    /**
     * @var array
     */
    public static $payload;

    /**
     * @return void
     */
    public static function init()
    {
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
        //self::init_payload();
        //self::init_file();
    }

    // common

    /**
     * @return bool
     */
    public static function is_cli()
    {
        return \php_sapi_name() === 'cli';
    }
    /**
     * @return bool
     */
    public static function is_cli_server()
    {
        return \php_sapi_name() === 'cli-server';
    }

    /**
     * @return string
     */
    public static function script_name()
    {
        return self::server('script_name');
    }

    /**
     * @return string
     */
    public static function method()
    {
        return strtolower((string) self::server('REQUEST_METHOD', 'get'));
    }

    /**
     * @param string $value
     * @return bool
     */
    public static function is_method($value)
    {
        return strtolower($value) === self::method();
    }
    
    /**
     * @return bool
     */
    public static function is_ajax() {
        return self::has_header('X_REQUESTED_WITH') &&
           strtolower((string) self::header('X_REQUESTED_WITH')) === 'xmlhttprequest';
    }

    /**
     * @return string
     */
    public static function ip()
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
    public static function user_agent()
    {
        return (string) self::header('user_agent');
    }

    /**
     * @return string
     */
    public static function referer()
    {
        return (string) self::header('referer');
    }

    /**
     * @return string
     */
    public static function host()
    {
        return (string) self::header('host');
    }
    /**
     * @return string
     */
    public static function base_url()
    {
        return self::server('request_scheme', 'http')
            . '://'
            . self::server('server_name', 'localhost');
    }

    /**
     * @param string $mode
     * @return string
     */
    public static function uri($mode = 'full')
    {
        $uri = self::server('request_uri');

        switch ($mode) {
            case 'request':
                return $uri;
            case 'no_query':
                return strtok($uri, '?');
            default:
                return self::base_url() . $uri;
        }
    }

    /**
     * @return string
     */
    public static function query_string()
    {
        return (string) self::server('query_string');
    }

    // HEADER
    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function header($key = '', $default = null)
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
    public static function has_header($key)
    {
        return isset($_SERVER['HTTP_' . strtoupper($key)]);
    }

    // GET

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($key = '', $default = null)
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
    public static function has_get($key)
    {
        return isset($_GET[$key]);
    }
    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public static function set_get($key, $value)
    {
        $_GET[$key] = $value;
    }

    // POST

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function post($key, $default = null)
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
    public static function has_post($key)
    {
        return isset($_POST[$key]);
    }
    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public static function set_post($key, $value)
    {
        $_POST[$key] = $value;
    }

    // COOKIE

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function cookie($key, $default = null)
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
    public static function has_cookie($key)
    {
        return isset($_COOKIE[$key]);
    }
    /**
     * @param string $key
     * @param string $value
     * @return void
     */
    public static function set_cookie($key, $value)
    {
        $_COOKIE[$key] = $value;
    }

    // SESSION

    /**
     * @param string $prefix
     * @param int $ttl
     * @return void
     */
    public static function session_start($prefix = 'sess_', $ttl = 86400)
    {
        //session_set_save_handler(new \ngatngay\session\storage\apcu($prefix, $ttl));

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
    public static function session($key, $default = null)
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
    public static function has_session($key)
    {
        return isset($_SESSION[$key]);
    }
    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public static function set_session($key, $value)
    {
        $_SESSION[$key] = $value;
    }
    /**
     * @param string $key
     * @return void
     */
    public static function unset_session($key)
    {
        unset($_SESSION[$key]);
    }


    // SERVER

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function server($key, $default = null)
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
    public static function has_server($key)
    {
        return isset($_SERVER[$key]) ? true : isset($_SERVER[strtoupper($key)]);
    }

    // FILES
    /**
     * @param string $key
     * @return array|null
     */
    public static function file($key)
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
    public static function request($key, $default = null)
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
    public static function has_request($key)
    {
        return isset($_REQUEST[$key]);
    }

    // PAYLOAD

    /**
     * @return void
     */
    public static function init_payload()
    {
        self::$payload = json::decode(file_get_contents('php://input') ?: '[]', true);
    }
    /**
     * @param string $key
     * @return bool
     */
    public static function has_payload($key)
    {
        return isset(self::$payload[$key]);
    }
    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function payload($key = '', $default = null)
    {
        if ($key === '') {
            return self::$payload;
        }

        return self::$payload[$key] ?? $default;
    }
}
