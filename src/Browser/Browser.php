<?php

namespace NgatNgay\Browser;

use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Component\BrowserKit\HttpBrowser;

class Browser extends HttpBrowser {
    public function __construct(?HttpClientInterface $client = null, ?History $history = null, ?CookieJar $cookieJar = null)
    {
        if (!$client && !class_exists(CurlHttpClient::class)) {
            throw new LogicException(\sprintf('You cannot use "%s" as the HttpClient component is not installed. Try running "composer require symfony/http-client".', __CLASS__));
        }

        $this->client = $client ?? new CurlHttpClient();

        parent::__construct([], $history, $cookieJar);
    }
}
