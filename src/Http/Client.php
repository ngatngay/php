<?php

namespace Nightmare\Http;

use Symfony\Component\HttpClient\CurlHttpClient;

class_alias(CurlHttpClient::class, 'Nightmare\Http\Client');
