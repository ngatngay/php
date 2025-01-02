<?php

namespace NgatNgay\Helper;

// array helper pro max

class Arr
{
    public static function multipleUploadToSimple(array $fileUpload): array
    {
        $files = [];

        foreach ($fileUpload as $k => $l) {
            foreach ($l as $i => $v) {
                if (!array_key_exists($i, $files)) {
                    $files[$i] = [];
                }

                $files[$i][$k] = $v;
            }
        }

        return $files;
    }

    public static function getFromPage(array $data, int $page, int $perPage = 10): array
    {
        $result = [];
        $total = count($data);
        $start = ($page - 1) * $perPage;
        $end = $start + $perPage;

        if ($start < 0) {
            $start = 0;
        }

        if ($end > $total) {
            $end = $total;
        }

        for ($start; $start < $end; $start++) {
            $result[] = $data[$start];
        }

        return $result;
    }

    public static function toFile(string $filename, array $arr) {
        return file_put_contents(
            $filename,
            '<?php return ' . var_export($arr, true) . ';'
        );
    }
}

?><?php

namespace NgatNgay\Helper;

use Symfony\Component\Cache\Adapter\ApcuAdapter;
use Symfony\Component\Cache\Adapter\ChainAdapter;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\Adapter\PdoAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\Request;
use Psr\Container\ContainerInterface;
use Illuminate\Database\Capsule\Manager as Capsule;
use Symfony\Component\Cache\Psr16Cache;

class Cache
{
    private static $adapter;
    private static array $adapters = [];
    private static ?int $expire = null;
    private static string $prefix = '';
    
    public static function setPrefix($prefix) {
        self::$prefix = $prefix;
    }

    public static function setDefaultAdapter($key)
    {
        self::$adapter = self::$adapters[$key];
    }
    
    public static function getAdaper($key)
    {
        return self::$adapters[$key];
    }
    
    public static function addAdapter($key, $adapter)
    {
        self::$adapters = array_merge(self::$adapters, [$key => $adapter]);
    }

    public function removeAdapter($key) {
        unset(self::$adapters[$key]);
    }
    
    public static function getAdapters()
    {
        return self::$adapters;
    }

    public static function has($key)
    {
        return self::$adapter->hasItem(self::$prefix . $key);
    }

    // truyen 1 tham so - lay binh thuong
    // truyen 2 tham so tro len - luu cache cho lan sau
    public static function get($key, ?callable $default = null, $expire = null)
    {
        $item = self::$adapter->getItem(self::$prefix . $key);
        
        if (!$item->isHit() && is_callable($default)) {
            $value = call_user_func($default);
            self::set($key, $value, $expire);         
            return $value;
        }

        return $item->get();
    }

    public static function set($key, $value, $expire = null)
    {
        $item = self::$adapter->getItem(self::$prefix . $key);

        if ($expire !== null) {
            $item->expiresAfter($expire);
        } else if (self::$expire !== null) {
            $item->expiresAfter(self::$expire);
        }

        $item->set($value);
        return self::$adapter->save($item);
    }

    public static function remove($key)
    {
        $key = self::$prefix . $key;
        return self::$adapter->deleteItem($key);
    }
    
    public static function setDefaultExpire($ttl = null)
    {
        self::$expire = $ttl;
    }
}

?><?php

namespace NgatNgay\Helper;

class Curl extends \Curl\Curl {
    public function __construct($base_url = null, $options = [])
    {
        parent::__construct($base_url, $options);
        
        $this->setJsonDecoder(function ($res) {
            return json_decode($res, true);
        });
    }
}

?><?php

namespace NgatNgay\Helper;

class Date
{
    public static function now()
    {
        return time();
    }

    public static function startDay($day)
    {
        return mktime(00, 00, 00, date('n'), $day);
    }

    public static function startMonth($month)
    {
        return mktime(00, 00, 00, $month);
    }

    public static function startYear()
    {
    }


    public static function currentDay()
    {
        return date('d');
    }

    public static function currentMonth()
    {
        return date('m');
    }

    public static function currentYear()
    {
        date('Y');
    }

    public static function displayAgo($time)
    {
        $time  = intval($time);
        $times = time() - $time;

        if ($times < 1) {
            $t = 'Vừa xong';
        } elseif ($times < 60) {
            $t = $times . ' giây trước';
        } elseif ($times < 3600) {
            $t = round($times / 60) . ' phút trước';
        } elseif ($times < 86400) {
            $t = round($times / 3600) . ' giờ trước';
        } elseif ($times < 2_592_000) {
            $t = round($times / 86400) . ' ngày trước';
        } elseif ($times < 31_536_000) {
            $t = round($times / 2_592_000) . ' tháng trước';
        } else {
            $t = round($times / 31_536_000) . ' năm trước';
        }

        return $t;
    }
}

?><?php

namespace NgatNgay\Helper;

// file system
class FS
{
    /*
     * file, file1, file2...
     */
    function nameIncrement(string $file_name_body, string $file_ext): string
    {
        $i = 1;
        $file_exists = true;

        do {
            $file_save = $file_name_body . $i . '.' . $file_ext;

            if (!file_exists($file_save)) {
                $file_exists = false;
            }

            $i++;
        } while ($file_exists);

        return $file_save;
    }

    public static function getExtension(string $name): string
    {
        $name = strval($name);

        preg_match('/\.([^\.]*$)/', $name, $extension);

        if (is_array($extension) && sizeof($extension) > 0) {
            return strtolower($extension[1]);
        }

        return '';
    }

    /**
     * @param $fileSize string
     * @return string
     */
    public static function readableSize($fileSize)
    {
        $size = floatval($fileSize);

        if ($size < 1024) {
            $s = $size . ' B';
        } elseif ($size < 1048576) {
            $s = round($size / 1024, 2) . ' KB';
        } elseif ($size < 1073741824) {
            $s = round($size / 1048576, 2) . ' MB';
        } elseif ($size < 1099511627776) {
            $s = round($size / 1073741824, 2) . ' GB';
        } elseif ($size < 1125899906842624) {
            $s = round($size / 1099511627776, 2) . ' TB';
        } elseif ($size < 1152921504606846976) {
            $s = round($size / 1125899906842624, 2) . ' PB';
        } elseif ($size < 1.1805916207174E+21) {
            $s = round($size / 1152921504606846976, 2) . ' EB';
        } elseif ($size < 1.2089258196146E+24) {
            $s = round($size / 1.1805916207174E+21, 2) . ' ZB';
        } else {
            $s = round($size / 1.2089258196146E+24, 2) . ' YB';
        }

        return $s;
    }

    public static function remove($path)
    {
        if (file_exists($path)) {
            unlink($path);
        }
    }

    public static function removeDir($dir, $remove_this = true)
    {
        if ($objs = glob($dir . "/*")) {
            foreach ($objs as $obj) {
                is_dir($obj) ? $this->removeDir($obj) : $this->remove($obj);
            }
        }

        if ($remove_this) {
            rmdir($dir);
        }
    }
}

?><?php

namespace NgatNgay\Helper;

class Request
{
    public array $get;
    public array $post;
    public array $header;
    public array $cookie;
    public array $server;
    public array $request;

    public function __construct()
    {
        $this->server = array_change_key_case($_SERVER);
        ksort($this->server);

        $this->header = $this->initHeader();

        $this->get = $_GET;
        $this->post = $_POST;
        $this->cookie = $_COOKIE;
        $this->request = $_REQUEST;
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

    public function isCLI()
    {
        return php_sapi_name() === 'cli';
    }
    public function isCLIServer()
    {
        return php_sapi_name() === 'cli-server';
    }

    public function getScriptName(): string
    {
        return $this->server('script_name');
    }


    public function getMethod()
    {
        return strtolower($this->server['request_method']);
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

        return '0.0.0.0';
    }

    public function getUserAgent()
    {
        return $this->header['user-agent'] ?? '';
    }

public function getReferer()
{
    return $this->header['referer'] ?? '';
}


    public function getBaseUrl()
    {
        return ($this->server['request_scheme'] ?? 'http')
            . '://'
            . ($this->server['server_name'] ?? 'localhost');
    }

    public function getUrl($mode = 'full')
    {
        $uri = $mode === 'no_query' ? strtok($this->server['request_uri'], '?') : $this->server['request_uri'];
        return $this->getBaseUrl() . $uri;
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
}

?><?php

namespace NgatNgay\Helper;

// best string helper

class Str
{
    /**
     * @param string $string
     * @return bool
     */
    public static function empty($string)
    {
        return strlen($string) === 0;
    }

    public static function wordCut(string $string, int $words = 35, string $end = '...'): string
    {
        preg_match('/^\s*+(?:\S++\s*+){1,' . $words . '}/u', $string, $matches);

        if (!isset($matches[0]) || self::length($string) === self::length($matches[0])) {
            return $string;
        }

        return rtrim($matches[0]) . $end;
    }

    public static function nl2br(string $str): string
    {
        return str_replace(PHP_EOL, '<br />', $str);
    }

    public static function br2nl(string $str): string
    {
        return preg_replace('#<br\s*/?>#i', PHP_EOL, $str);
    }

    public static function length(string $str): int
    {
        return mb_strlen($str);
    }

    /**
     * Chuyển đổi tiếng Việt sang tiếng Anh
     * @param string $str
     * @return string
     */
    public static function vn2en($str)
    {
        $unicode = [
            'a' => '/á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ/',
            'd' => '/đ/',
            'e' => '/é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ/',
            'i' => '/í|ì|ỉ|ĩ|ị/',
            'o' => '/ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ/',
            'u' => '/ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự/',
            'y' => '/ý|ỳ|ỷ|ỹ|ỵ/',
            'A' => '/Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ằ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ/',
            'D' => '/Đ/',
            'E' => '/É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ/',
            'I' => '/Í|Ì|Ỉ|Ĩ|Ị/',
            'O' => '/Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ/',
            'U' => '/Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự/',
            'Y' => '/Ý|Ỳ|Ỷ|Ỹ|Ỵ/'
        ];

        return preg_replace(array_values($unicode), array_keys($unicode), $str);
    }
}

?><?php

namespace NgatNgay\Helper;

function request(): Request
{
    static $instance = null;

    if ($instance === null) {
        $instance = new Request();
    }

    return $instance;
}

interface IResponse
{
    public function data($data);
    public function status($status);
    public function headers($headers);
    public function json();
    public function send();
}
function response($data = null, $status = 200, $headers = []): IResponse
{
    return new class ($data, $status, $headers) implements IResponse {
        public function __construct(
            private $data,
            private $status,
            private array $headers = []
        ) {
        }

        public function data($data)
        {
            $this->data = $data;
            return $this;
        }
        public function status($status)
        {
            $this->status = $status;
            return $this;
        }
        public function json()
        {
            $this->headers += ['Content-Type: application/json'];

            if (is_array($this->data)) {
                $this->data = json_encode($this->data, JSON_PRETTY_PRINT);
            }
            return $this;
        }

        public function headers($headers)
        {
            $this->headers = $headers;
            return $this;
        }

        public function send()
        {
            if (is_array($this->data)) {
                $this->json();
            }

            http_response_code($this->status);

            $this->headers = array_unique($this->headers);
            foreach ($this->headers as $header) {
                header($header);
            }

            exit($this->data);
        }
    };
}

function redirect(string $url, int $status = 301)
{
    ob_end_clean();
    http_response_code($status);
    header('Location: ' . $url);
    exit;
}

function refresh()
{
    ob_end_clean();
    header('Refresh:0');
    exit;
}

?>