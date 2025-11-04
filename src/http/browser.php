<?php

namespace nightmare\http;

use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\BrowserKit\History;
use Symfony\Component\BrowserKit\CookieJar;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class browser extends HttpBrowser
{
    /**
     * @param HttpClientInterface|null $client
     * @param History|null $history
     * @param CookieJar|null $cookieJar
     */
    public function __construct($client = null, $history = null, $cookieJar = null)
    {
        parent::__construct($client ?? new client(), $history, $cookieJar); // @phpstan-ignore-line
    }
    
    /**
     * @param string $userAgent
     * @return void
     */
    public function setUserAgent($userAgent) {
        $this->setServerParameter('HTTP_USER_AGENT', $userAgent);
    }
}
