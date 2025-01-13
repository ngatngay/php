<?php

namespace NgatNgay\Http;

class Response {
    private $data;
    private $status;
    private array $headers = [];

    public function __construct(
        $data,
        $status,
        $headers = []
    ) {
        $this->data = $data;
        $this->status = $status;
        $this->headers = $headers;
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
}