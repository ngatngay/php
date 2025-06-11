<?php

namespace ngatngay\http;

class request
{
    public static array $file;
    public static array $header;
    public static array $server;
    public static array $payload;

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

    public static function is_cli(): bool
    {
        return \php_sapi_name() === 'cli';
    }
    public static function is_cli_server(): bool
    {
        return \php_sapi_name() === 'cli-server';
    }

    public static function script_name(): string
    {
        return self::server('script_name');
    }

    public static function method()
    {
        return strtolower(self::server('REQUEST_METHOD', 'get'));
    }

    public static function is_method(string $value)
    {
        return strtolower($value) === self::method();
    }

    public static function client_ip()
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

    public static function user_agent()
    {
        return (string) self::header('user_agent');
    }

    public static function referer()
    {
        return (string) self::header('referer');
    }

    public static function host()
    {
        return (string) self::header('host');
    }
    public static function base_url()
    {
        return self::server('request_scheme', 'http')
            . '://'
            . self::server('server_name', 'localhost');
    }

    public static function uri(string $mode = 'full')
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

    public static function query_string()
    {
        return (string) self::server('query_string');
    }

    // HEADER
    public static function header($key, $default = null)
    {
        return $_SERVER['HTTP_' . strtoupper($key)] ?? $default;
    }
    public static function has_header($key)
    {
        return isset($_SERVER['HTTP_' . strtoupper($key)]);
    }

    // GET

    public static function get($key, $default = null)
    {
        return $_GET[$key] ?? $default;
    }
    public static function has_get($key)
    {
        return isset($_GET[$key]);
    }
    public static function set_get($key, $value)
    {
        $_GET[$key] = $value;
    }

    // POST

    public static function post($key, $default = null)
    {
        return $_POST[$key] ?? $default;
    }
    public static function has_post($key)
    {
        return isset($_POST[$key]);
    }
    public static function set_post($key, $value)
    {
        $_POST[$key] = $value;
    }

    // COOKIE

    public static function cookie($key, $default = null)
    {
        return $_COOKIE[$key] ?? $default;
    }
    public static function has_cookie($key)
    {
        return isset($_COOKIE[$key]);
    }
    public static function set_cookie($key, $value)
    {
        $_COOKIE[$key] = $value;
    }

    // SESSION

    public static function session_start(string $prefix = 'sess_', int $ttl = 86400)
    {
        //session_set_save_handler(new \ngatngay\session\storage\apcu($prefix, $ttl));

        if (PHP_SESSION_ACTIVE === session_status()) {
            throw new \RuntimeException('Failed to start the session: already started by PHP.');
        }

        if (!\session_start()) {
            throw new \RuntimeException('Failed to start the session.');
        }
    }
    public static function session($key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }
    public static function has_session($key)
    {
        return isset($_SESSION[$key]);
    }
    public static function set_session($key, $value)
    {
        $_SESSION[$key] = $value;
    }
    public static function unset_session($key)
    {
        unset($_SESSION[$key]);
    }


    // SERVER

    public static function server($key, $default = null)
    {
        return isset($_SERVER[$key]) ? $_SERVER[$key] : (isset($_SERVER[strtoupper($key)]) ? $_SERVER[strtoupper($key)] : $default);
    }
    public static function has_server($key): bool
    {
        return isset($_SERVER[$key]) ? true : isset($_SERVER[strtoupper($key)]);
    }

    // FILES
    private static function init_file()
    {
        if (empty(self::$file)) {
            return;
        }

        $tmp = [];
        foreach (self::$file as $key => $value) {
            if (!is_array(self::$file[$key]['name'])) {
                $tmp[$key] = [self::$file[$key]];
                continue;
            }

            $fCount = count(self::$file[$key]['name']);
            $fKeys = array_keys(self::$file[$key]);

            for ($i = 0; $i < $fCount; $i++) {
                foreach ($fKeys as $fKey) {
                    $tmp[$key][$i][$fKey] = self::$file[$key][$fKey][$i];
                }
            }
        }

        self::$file = $tmp;
    }

    public static function file($key)
    {
        if (!isset($_FILES[$$key])) {
            return null;
        }

        if (!is_array($_FILES[$key]['name'])) {
            return [$_FILES[$key]];
        }

        $tmp = [];
        foreach (self::$file as $key => $value) {
            if (!is_array(self::$file[$key]['name'])) {
                $tmp[$key] = [self::$file[$key]];
                continue;
            }

            $fCount = count(self::$file[$key]['name']);
            $fKeys = array_keys(self::$file[$key]);

            for ($i = 0; $i < $fCount; $i++) {
                foreach ($fKeys as $fKey) {
                    $tmp[$key][$i][$fKey] = self::$file[$key][$fKey][$i];
                }
            }
        }

    }

    // REQUEST

    public static function request($key, $default = null)
    {
        return $_REQUEST[$key] ?? $default;
    }
    public static function has_request($key)
    {
        return isset($_REQUEST[$key]);
    }

    // PAYLOAD

    private static function init_payload()
    {
        $data = @json_decode(file_get_contents('php://input'), true);
        if (json_last_error() === \JSON_ERROR_NONE) {
            self::$payload = $data;
        }
    }
    public static function has_payload($key)
    {
        return isset(self::$payload[$key]);
    }
    public static function payload($key, $default = null)
    {
        return self::$payload[$key] ?? $default;
    }
}
