<?php

namespace Concrete\Core\API\Oauth\Grant;

use League\OAuth2\Server\ResponseTypes\ResponseTypeInterface;
use Psr\Http\Message\ServerRequestInterface;
use League\OAuth2\Server\Grant\ClientCredentialsGrant as BaseClientCredentialsGrant;
/**
 * Client credentials grant class.
 */
class ClientCredentialsGrant extends BaseClientCredentialsGrant
{
    /**
     * {@inheritdoc}
     */
    public function respondToAccessTokenRequest(
        ServerRequestInterface $request,
        ResponseTypeInterface $responseType,
        \DateInterval $accessTokenTTL
    ) {
        // Validate request
        $client = $this->validateClient($request);
        $scopes = $this->validateScopes($this->getRequestParameter('scope', $request));

        // Finalize the requested scopes
        $scopes = $this->scopeRepository->finalizeScopes($scopes, $this->getIdentifier(), $client);

        // Issue and persist access token
        $accessToken = $this->issueAccessToken($accessTokenTTL, $client, $client->getUser()->getUserID(), $scopes);

        // Inject access token into response type
        $responseType->setAccessToken($accessToken);

        return $responseType;
    }

}
