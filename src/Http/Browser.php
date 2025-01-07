<?php

namespace NgatNgay\Http;

use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\BrowserKit\History;
use Symfony\Component\BrowserKit\CookieJar;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Browser extends HttpBrowser {
    public function __construct(?HttpClientInterface $client = null, ?History $history = null, ?CookieJar $cookieJar = null)
    {
        parent::__construct($client ?? new Client(), $history, $cookieJar);
    }
    
    public function setUserAgent(string $userAgent) {
        $this->setServerParameter('HTTP_USER_AGENT', $userAgent);
    }
}
