<?php

namespace Concrete\Core\Entity\OAuth;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;

class AccessTokenRepository extends EntityRepository implements AccessTokenRepositoryInterface
{

    /**
     * Create a new access token
     *
     * @param ClientEntityInterface $clientEntity
     * @param ScopeEntityInterface[] $scopes
     * @param mixed $userIdentifier
     *
     * @return AccessTokenEntityInterface
     */
    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null)
    {
        $token = new AccessToken();
        $token->setClient($clientEntity);
        foreach ($scopes as $scope) {
            $token->addScope($scope);
        }


        $token->setUserIdentifier($userIdentifier);

        return $token;
    }

    /**
     * Persists a new access token to permanent storage.
     *
     * @param AccessTokenEntityInterface $accessTokenEntity
     * @throws \Exception
     */
    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity)
    {
        $this->getEntityManager()->transactional(function(EntityManagerInterface $em) use ($accessTokenEntity) {
            $em->persist($accessTokenEntity);
        });
    }

    /**
     * Revoke an access token.
     *
     * @param string $tokenId
     * @throws \Exception
     */
    public function revokeAccessToken($tokenId)
    {
        if ($token = $this->find($tokenId)) {
            $this->getEntityManager()->transactional(function(EntityManagerInterface $em) use ($token) {
                $em->remove($token);
            });
        }
    }

    /**
     * Check if the access token has been revoked.
     *
     * @param string $tokenId
     *
     * @return bool Return true if this token has been revoked
     */
    public function isAccessTokenRevoked($tokenId)
    {
        /** @var \Concrete\Core\Entity\OAuth\AccessToken $token */
        $token = $this->find($tokenId);
        if (!$token) {
            // The token was manually removed.
            return true;
        }

        $now = new \DateTime('now');
        // If we have a token and it has expired...
        if ($token && $token->getExpiryDateTime() < $now) {
            return true;
        }

        return false;
    }
}
