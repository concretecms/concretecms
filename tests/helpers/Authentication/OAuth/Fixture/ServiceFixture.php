<?php

namespace Concrete\TestHelpers\Authentication\OAuth\Fixture;

use OAuth\Common\Service\ServiceInterface;

class ServiceFixture implements ServiceInterface
{
    public function request($path, $method = 'GET', $body = null, array $extraHeaders = [])
    {
    }

    public function getAuthorizationUri(array $additionalParameters = [])
    {
    }

    public function getAuthorizationEndpoint()
    {
    }

    public function getAccessTokenEndpoint()
    {
    }
}
