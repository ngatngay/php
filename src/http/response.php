<?php

namespace ngatngay\http;

class response
{
    private $data;
    /**
     * @var int
     */
    private $status;
    /**
     * @var array
     */
    private $headers = [];
    /**
     * @var bool
     */
    private static $is_sended = false;

    /**
     * @param mixed $data
     * @param int $status
     * @param array $headers
     */
    public function __construct(
        $data = null,
        $status = 200,
        $headers = []
    ) {
        $this->data = $data;
        $this->status = $status;
        $this->headers = $headers;
    }

    /**
     * @param mixed $data
     * @return self
     */
    public function data($data)
    {
        $this->data = $data;
        return $this;
    }
    /**
     * @param int $status
     * @return self
     */
    public function status($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @param bool $prettify
     * @return self
     */
    public function json($prettify = false)
    {
        $this->headers += ['Content-Type: application/json'];

        if (is_array($this->data) || is_object($this->data)) {
            $flags = 0;

            $flags |= JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;

            if ($prettify) {
                $flags |= JSON_PRETTY_PRINT;
            }

            $this->data = json_encode($this->data, $flags);
        }

        return $this;
    }

    /**
     * @param array $headers
     * @return self
     */
    public function headers($headers)
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * @return void
     */
    public function send()
    {
        static $is_sended;

        if ($is_sended) {
            return;
        } else {
            $is_sended = true;
        }

        if (is_array($this->data)) {
            $this->json();
        }

        http_response_code($this->status);

        $this->headers = array_unique($this->headers);
        foreach ($this->headers as $header) {
            header($header);
        }

        echo $this->data;

        if (\function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        } elseif (\function_exists('litespeed_finish_request')) {
            litespeed_finish_request();
        } elseif (!\in_array(\PHP_SAPI, ['cli', 'phpdbg', 'embed'], true)) {
            static::close_output_buffers(0, true);
            flush();
        }
    }

    /**
     * @param int $targetLevel
     * @param bool $flush
     * @return void
     */
    public static function close_output_buffers($targetLevel, $flush)
    {
        $status = ob_get_status(true);
        $level = \count($status);
        $flags = \PHP_OUTPUT_HANDLER_REMOVABLE | ($flush ? \PHP_OUTPUT_HANDLER_FLUSHABLE : \PHP_OUTPUT_HANDLER_CLEANABLE);

        while ($level-- > $targetLevel && ($s = $status[$level]) && (!isset($s['del']) ? !isset($s['flags']) || ($s['flags'] & $flags) === $flags : $s['del'])) {
            if ($flush) {
                ob_end_flush();
            } else {
                ob_end_clean();
            }
        }
    }
}
