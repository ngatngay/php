<?php

namespace ngatngay\http;

class response
{
    private mixed $data;
    private int $status;
    private array $headers = [];
    private static bool $is_sended = false;

    public function __construct(
        mixed $data = null,
        int $status = 200,
        array $headers = []
    ) {
        $this->data = $data;
        $this->status = $status;
        $this->headers = $headers;
    }

    public function data(mixed $data): self
    {
        $this->data = $data;
        return $this;
    }
    public function status(int $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function json(bool $prettify = false): self
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

    public function headers(array $headers): self
    {
        $this->headers = $headers;
        return $this;
    }

    public function send(): void
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

    public static function close_output_buffers(int $targetLevel, bool $flush): void
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
