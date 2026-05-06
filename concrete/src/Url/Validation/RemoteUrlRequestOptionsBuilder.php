<?php

namespace Concrete\Core\Url\Validation;

use GuzzleHttp\RequestOptions;

class RemoteUrlRequestOptionsBuilder
{
    public function build(ValidatedRemoteUrl $validatedUrl): array
    {
        return [
            RequestOptions::ALLOW_REDIRECTS => false,
            'curl' => [CURLOPT_RESOLVE => [sprintf('%s:%d:%s', $validatedUrl->getHost(), $validatedUrl->getPort(), $validatedUrl->getIp())]],
        ];
    }
}
