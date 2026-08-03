<?php

namespace Concrete\Core\Api\OAuth\Grant;

use Concrete\Core\Entity\User\User as UserEntity;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Grant\RefreshTokenGrant as BaseRefreshTokenGrant;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use League\OAuth2\Server\RequestEvent;
use Psr\Http\Message\ServerRequestInterface;

class RefreshTokenGrant extends BaseRefreshTokenGrant
{
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    public function __construct(RefreshTokenRepositoryInterface $refreshTokenRepository, UserRepositoryInterface $userRepository)
    {
        parent::__construct($refreshTokenRepository);
        $this->userRepository = $userRepository;
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

        $user = $this->userRepository->find($refreshTokenData['user_id']);

        if (!$user instanceof UserEntity || !$user->isUserActive()) {
            $this->getEmitter()->emit(new RequestEvent(RequestEvent::USER_AUTHENTICATION_FAILED, $request));
            throw OAuthServerException::invalidRefreshToken('Token is not linked to an active user');
        }

        return $refreshTokenData;
    }
}
