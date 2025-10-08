<?php

namespace ngatngay\http;

use ngatngay\json;
use RuntimeException;

class request
{
    public static array $file;
    public static array $header;
    public static array $server;
    public static array $payload;

    public static function init(): void
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

    public static function method(): string
    {
        return strtolower((string) self::server('REQUEST_METHOD', 'get'));
    }

    public static function is_method(string $value): bool
    {
        return strtolower($value) === self::method();
    }
    
    public static function is_ajax(): bool {
        return self::has_header('X_REQUESTED_WITH') &&
           strtolower((string) self::header('X_REQUESTED_WITH')) === 'xmlhttprequest';
    }

    public static function ip(): string
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

    public static function user_agent(): string
    {
        return (string) self::header('user_agent');
    }

    public static function referer(): string
    {
        return (string) self::header('referer');
    }

    public static function host(): string
    {
        return (string) self::header('host');
    }
    public static function base_url(): string
    {
        return self::server('request_scheme', 'http')
            . '://'
            . self::server('server_name', 'localhost');
    }

    public static function uri(string $mode = 'full'): string
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

    public static function query_string(): string
    {
        return (string) self::server('query_string');
    }

    // HEADER
    public static function header(string $key = '', mixed $default = null)
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
    public static function has_header(string $key): bool
    {
        return isset($_SERVER['HTTP_' . strtoupper($key)]);
    }

    // GET

    public static function get(string $key = '', mixed $default = null)
    {
        if ($key === '') {
            return $_GET;
        }

        return $_GET[$key] ?? $default;
    }
    public static function has_get(string $key): bool
    {
        return isset($_GET[$key]);
    }
    public static function set_get(string $key, mixed $value): void
    {
        $_GET[$key] = $value;
    }

    // POST

    public static function post(string $key, mixed $default = null)
    {
        if ($key === '') {
            return $_POST;
        }

        return $_POST[$key] ?? $default;
    }
    public static function has_post(string $key): bool
    {
        return isset($_POST[$key]);
    }
    public static function set_post(string $key, mixed $value): void
    {
        $_POST[$key] = $value;
    }

    // COOKIE

    public static function cookie(string $key, mixed $default = null)
    {
        if ($key === '') {
            return $_COOKIE;
        }

        return $_COOKIE[$key] ?? $default;
    }
    public static function has_cookie(string $key): bool
    {
        return isset($_COOKIE[$key]);
    }
    public static function set_cookie(string $key, string $value): void
    {
        $_COOKIE[$key] = $value;
    }

    // SESSION

    public static function session_start(string $prefix = 'sess_', int $ttl = 86400): void
    {
        //session_set_save_handler(new \ngatngay\session\storage\apcu($prefix, $ttl));

        if (PHP_SESSION_ACTIVE === session_status()) {
            throw new RuntimeException('Failed to start the session: already started by PHP.');
        }

        if (!\session_start()) {
            throw new RuntimeException('Failed to start the session.');
        }
    }
    public static function session(string $key, mixed $default = null)
    {
        if ($key === '') {
            return $_SESSION;
        }

        return $_SESSION[$key] ?? $default;
    }
    public static function has_session(string $key): bool
    {
        return isset($_SESSION[$key]);
    }
    public static function set_session(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }
    public static function unset_session(string $key): void
    {
        unset($_SESSION[$key]);
    }


    // SERVER

    public static function server(string $key, mixed $default = null)
    {
        if ($key === '') {
            return $_SERVER;
        }

        return (string) (isset($_SERVER[$key]) ? $_SERVER[$key] : (isset($_SERVER[strtoupper($key)]) ? $_SERVER[strtoupper($key)] : $default));
    }
    public static function has_server(string $key): bool
    {
        return isset($_SERVER[$key]) ? true : isset($_SERVER[strtoupper($key)]);
    }

    // FILES
    public static function file(string $key): ?array
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

    public static function request(string $key, mixed $default = null)
    {
        if ($key === '') {
            return $_REQUEST;
        }

        return $_REQUEST[$key] ?? $default;
    }
    public static function has_request(string $key): bool
    {
        return isset($_REQUEST[$key]);
    }

    // PAYLOAD

    public static function init_payload(): void
    {
        self::$payload = json::decode(file_get_contents('php://input') ?: '[]', true);
    }
    public static function has_payload(string $key): bool
    {
        return isset(self::$payload[$key]);
    }
    public static function payload(string $key = '', mixed $default = null)
    {
        if ($key === '') {
            return self::$payload;
        }

        return self::$payload[$key] ?? $default;
    }
}
