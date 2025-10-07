<?php

namespace ngatngay\http;

class curl extends \Curl\Curl {
    public function __construct(?string $base_url = null, array $options = [])
    {
        parent::__construct($base_url, $options);
        $this->setDefaultJsonDecoder($assoc = true);
    }
}
