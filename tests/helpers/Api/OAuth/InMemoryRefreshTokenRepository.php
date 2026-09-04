<?php

namespace Concrete\TestHelpers\Api\OAuth;

use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;

/**
 * A refresh token repository that actually remembers what has been revoked.
 *
 * Revocation has to persist between calls for a test to be able to show that a rejected refresh token
 * is genuinely dead rather than merely refused once.
 */
class InMemoryRefreshTokenRepository implements RefreshTokenRepositoryInterface
{

    /**
     * Token IDs known to this repository.
     *
     * @var bool[]
     */
    protected $tokens = [];

    /**
     * Token IDs that have been revoked, in the order they were revoked.
     *
     * @var string[]
     */
    public $revoked = [];

    /**
     * @param string ...$tokenIds token IDs that start out live
     */
    public function __construct(...$tokenIds)
    {
        foreach ($tokenIds as $tokenId) {
            $this->tokens[$tokenId] = true;
        }
    }

    /**
     * Not exercised by these tests - refresh tokens are seeded through the constructor.
     */
    public function getNewRefreshToken()
    {
        return null;
    }

    /**
     * Not exercised by these tests.
     */
    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity)
    {
    }

    public function revokeRefreshToken($tokenId)
    {
        unset($this->tokens[$tokenId]);
        $this->revoked[] = $tokenId;
    }

    public function isRefreshTokenRevoked($tokenId)
    {
        return !isset($this->tokens[$tokenId]);
    }
}
