<?php

namespace nightmare\http;

use Symfony\Component\HttpClient\CurlHttpClient;

class_alias(CurlHttpClient::class, 'nightmare\http\client');
