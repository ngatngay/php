<?php

namespace NgatNgay\Http;

class Curl extends \Curl\Curl {
    public function __construct($base_url = null, $options = [])
    {
        parent::__construct($base_url, $options);
        $this->setDefaultJsonDecoder($assoc = true);
    }
}
