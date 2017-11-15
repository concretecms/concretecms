<?php
namespace Concrete\Tests\Core\Authentication\Fixtures;

class ServiceFixture implements \OAuth\Common\Service\ServiceInterface
{

    public function request($path, $method = 'GET', $body = null, array $extraHeaders = array())
    {
    }

    public function getAuthorizationUri(array $additionalParameters = array())
    {
    }

    public function getAuthorizationEndpoint()
    {
    }

    public function getAccessTokenEndpoint()
    {
    }

}
