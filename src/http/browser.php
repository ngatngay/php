<?php

namespace ngatngay\http;

use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\BrowserKit\History;
use Symfony\Component\BrowserKit\CookieJar;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class browser extends HttpBrowser
{
    public function __construct(?HttpClientInterface $client = null, ?History $history = null, ?CookieJar $cookieJar = null)
    {
        parent::__construct($client ?? new client(), $history, $cookieJar); // @phpstan-ignore-line
    }
    
    public function setUserAgent(string $userAgent): void {
        $this->setServerParameter('HTTP_USER_AGENT', $userAgent);
    }
}
