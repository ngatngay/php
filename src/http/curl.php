<?php

namespace nightmare\http;

class curl extends \Curl\Curl {
    /**
     * @param string|null $base_url
     * @param array $options
     */
    public function __construct($base_url = null, $options = [])
    {
        parent::__construct($base_url, $options);
        $this->setDefaultJsonDecoder($assoc = true);
    }
}
