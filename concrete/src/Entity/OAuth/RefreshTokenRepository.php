<?php

namespace Concrete\Core\Entity\OAuth;

use Concrete\Core\Entity\Express\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;

class RefreshTokenRepository extends EntityRepository implements RefreshTokenRepositoryInterface
{

    /**
     * Creates a new refresh token
     *
     * @return RefreshTokenEntityInterface
     */
    public function getNewRefreshToken()
    {
        return new RefreshToken();
    }

    /**
     * Create a new refresh token_name.
     *
     * @param RefreshTokenEntityInterface $refreshTokenEntity
     * @throws \Exception
     */
    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity)
    {
        $this->getEntityManager()->transactional(function(EntityManagerInterface $em) use ($refreshTokenEntity) {
            $em->persist($refreshTokenEntity);
        });
    }

    /**
     * Revoke the refresh token.
     *
     * @param string $tokenId
     * @throws \Exception
     */
    public function revokeRefreshToken($tokenId)
    {
        $this->getEntityManager()->transactional(function(EntityManagerInterface $em) use ($tokenId) {
            $token = $this->find($tokenId);

            if ($token) {
                $em->remove($token);
            }
        });
    }

    /**
     * Check if the refresh token has been revoked.
     *
     * @param string $tokenId
     *
     * @return bool Return true if this token has been revoked
     */
    public function isRefreshTokenRevoked($tokenId)
    {
        return $this->find($tokenId) === null;
    }
}
