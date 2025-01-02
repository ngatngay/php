<?php

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
