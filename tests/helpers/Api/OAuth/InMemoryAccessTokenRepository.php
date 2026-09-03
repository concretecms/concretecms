<?php

namespace Concrete\TestHelpers\Api\OAuth;

use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;

/**
 * An access token repository that actually remembers what has been revoked.
 *
 * @see \Concrete\TestHelpers\Api\OAuth\InMemoryRefreshTokenRepository
 */
class InMemoryAccessTokenRepository implements AccessTokenRepositoryInterface
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
     * Not exercised by these tests - access tokens are seeded through the constructor.
     */
    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null)
    {
        return null;
    }

    /**
     * Not exercised by these tests.
     */
    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity)
    {
    }

    public function revokeAccessToken($tokenId)
    {
        unset($this->tokens[$tokenId]);
        $this->revoked[] = $tokenId;
    }

    public function isAccessTokenRevoked($tokenId)
    {
        return !isset($this->tokens[$tokenId]);
    }
}
