<?php

namespace ngatngay\http;

class curl extends \Curl\Curl {
    public function __construct($base_url = null, $options = [])
    {
        parent::__construct($base_url, $options);
        $this->setDefaultJsonDecoder($assoc = true);
    }
}
