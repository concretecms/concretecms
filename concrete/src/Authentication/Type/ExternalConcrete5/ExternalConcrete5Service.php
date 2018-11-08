<?php

namespace Concrete\Core\Authentication\Type\ExternalConcrete5;

use Concrete\Core\Entity\OAuth\AccessToken;
use League\OAuth2\Server\Exception\OAuthServerException;
use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\Common\Http\Uri\UriInterface;
use OAuth\OAuth2\Service\AbstractService;
use OAuth\OAuth2\Token\StdOAuth2Token;
use OAuth\OAuth2\Token\TokenInterface;

class ExternalConcrete5Service extends AbstractService
{

    /** @var string Scope for system info */
    const SCOPE_SYSTEM = 'system';

    /** @var string Scope for site tree info */
    const SCOPE_SITE = 'site';

    /** @var string Scope for authenticated user */
    const SCOPE_ACCOUNT = 'account';

    /** @var string Authorization path */
    const PATH_AUTHORIZE = '/oauth/2.0/authorize';

    /** @var string Token path */
    const PATH_TOKEN = '/oauth/2.0/token';

    /**
     * Parses the access token response and returns a TokenInterface.
     *
     *
     * @param string $responseBody
     * @return TokenInterface
     * @throws TokenResponseException
     */
    protected function parseAccessTokenResponse($responseBody)
    {
        $body = json_decode($responseBody, true);

        if (isset($body['error'])) {
            throw new TokenResponseException($body['hint']);
        }

        $token = new StdOAuth2Token();
        $token->setAccessToken($body['access_token']);
        $token->setRefreshToken($body['refresh_token']);
        $token->setLifetime($body['expires_in']);

        return $token;
    }

    /**
     * Returns the authorization API endpoint.
     *
     * @return UriInterface
     */
    public function getAuthorizationEndpoint()
    {
        $uri = $this->getBaseApiUri();
        $uri->setPath(self::PATH_AUTHORIZE);

        return $uri;
    }

    /**
     * Returns the access token API endpoint.
     *
     * @return UriInterface
     */
    public function getAccessTokenEndpoint()
    {
        $uri = $this->getBaseApiUri();
        $uri->setPath(self::PATH_TOKEN);

        return $uri;
    }

    /**
     * Return a copy of our base api uri
     *
     * @return \OAuth\Common\Http\Uri\UriInterface
     */
    public function getBaseApiUri()
    {
        return clone $this->baseApiUri;
    }

    /**
     * Declare that we use the bearer header field
     * We want our headers to be:
     *     Authorization: Bearer SOMETOKEN
     *
     * If we didn't declare this everything would break because they'd be
     *     Authorization: Bearer OAuth SOMETOKEN
     *
     * @return int
     */
    protected function getAuthorizationMethod()
    {
        return self::AUTHORIZATION_METHOD_HEADER_BEARER;
    }
}
