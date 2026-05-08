<?php

namespace Concrete\Core\Url\Validation;

use GuzzleHttp\RequestOptions;

class RemoteUrlRequestOptionsBuilder
{
    public function build(ValidatedRemoteUrl $validatedUrl): array
    {
        $url = $validatedUrl->getUrl();
        $host = trim((string) $url->getHost());
        $scheme = strtolower((string) $url->getScheme());
        $port = $url->getPort();
        $port = $port ? $port->get() : null;
        $port = $port ? (int) $port : ($scheme === 'http' ? 80 : 443);

        return [
            RequestOptions::ALLOW_REDIRECTS => false,
            'curl' => [CURLOPT_RESOLVE => [sprintf('%s:%d:%s', $host, $port, $validatedUrl->getIp())]],
        ];
    }
}
