<?php

namespace Concrete\Core\Api\OAuth\Grant;

use Concrete\Core\Api\OAuth\UserStatusValidator;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Grant\RefreshTokenGrant as BaseRefreshTokenGrant;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use League\OAuth2\Server\RequestEvent;
use Psr\Http\Message\ServerRequestInterface;

class RefreshTokenGrant extends BaseRefreshTokenGrant
{

    /**
     * @var \Concrete\Core\Api\OAuth\UserStatusValidator
     */
    private $userStatusValidator;

    public function __construct(
        RefreshTokenRepositoryInterface $refreshTokenRepository,
        UserRepositoryInterface $userRepository,
        UserStatusValidator $userStatusValidator
    ) {
        parent::__construct($refreshTokenRepository);
        $this->setUserRepository($userRepository);
        $this->userStatusValidator = $userStatusValidator;
    }

    /**
     * {@inheritdoc}
     *
     * The base implementation only checks that the refresh token itself is valid (not expired/revoked
     * and issued to the requesting client). It never re-checks the user the token was issued to, so a
     * token issued before a user is deactivated or deleted can still be used to mint new access tokens.
     */
    protected function validateOldRefreshToken(ServerRequestInterface $request, $clientId)
    {
        $refreshTokenData = parent::validateOldRefreshToken($request, $clientId);

        if (!$this->userStatusValidator->isValid($refreshTokenData['user_id'])) {
            // Revoke before throwing. The parent only revokes the old tokens further along in
            // respondToAccessTokenRequest(), so bailing out here would otherwise leave both the refresh
            // token and the access token it was paired with live - retryable indefinitely, and usable
            // again if the account is ever reactivated.
            $this->refreshTokenRepository->revokeRefreshToken($refreshTokenData['refresh_token_id']);
            $this->accessTokenRepository->revokeAccessToken($refreshTokenData['access_token_id']);

            $this->getEmitter()->emit(new RequestEvent(RequestEvent::USER_AUTHENTICATION_FAILED, $request));
            throw OAuthServerException::invalidRefreshToken('Token is not linked to an active user');
        }

        return $refreshTokenData;
    }
}
