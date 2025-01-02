<?php

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
