<?php

namespace Concrete\Core\Api\OAuth\Scope;

use Concrete\Core\Entity\OAuth\Scope;

/**
 * @since 8.5.2
 */
interface ScopeRegistryInterface
{
    /**
     * Returns an array of scopes that this registry makes available
     * 
     * @return Scope[]
     */
    public function getScopes();
}
