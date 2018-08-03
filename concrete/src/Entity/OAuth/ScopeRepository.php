<?php

namespace Concrete\Core\Entity\OAuth;

use Concrete\Core\Entity\Express\EntityRepository;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;

class ScopeRepository extends EntityRepository implements ScopeRepositoryInterface
{

    private $allowedScopes = [
        'system',
        'site'
    ];

    /**
     * Return information about a scope.
     *
     * @param string $identifier The scope identifier
     *
     * @return ScopeEntityInterface
     */
    public function getScopeEntityByIdentifier($identifier)
    {
        return $this->find($identifier);
    }

    /**
     * Given a client, grant type and optional user identifier validate the set of scopes requested are valid and optionally
     * append additional scopes or remove requested scopes.
     *
     * @param ScopeEntityInterface[] $scopes
     * @param string $grantType
     * @param ClientEntityInterface $clientEntity
     * @param null|string $userIdentifier
     *
     * @return ScopeEntityInterface[]
     */
    public function finalizeScopes(
        array $scopes,
        $grantType,
        ClientEntityInterface $clientEntity,
        $userIdentifier = null
    ) {
        $winnowed = [];
        foreach ($scopes as $scope) {
            if (in_array($scope->getIdentifier(), $this->allowedScopes)) {
                $winnowed[] = $scope;
            }
        }

        return $winnowed;
    }
}
