<?php

namespace Concrete\Core\Api\OAuth\Scope;

use Concrete\Core\Entity\OAuth\Scope;

class ScopeRegistry implements ScopeRegistryInterface
{
    
    protected function buildScope($identifier, $description)
    {
        $scope = new Scope();
        $scope->setDescription($description);
        $scope->setIdentifier($identifier);
        return $scope;
    }
    
    /**
     * @inheritdoc
     */
    public function getScopes()
    {
        return [
            $this->buildScope('openid', 'Remotely authenticate into Concrete.'),
            $this->buildScope('account:read', 'Read information about the remotely authenticated user.'),
            $this->buildScope('files:read', 'Read detailed information about uploaded files.'),
            $this->buildScope('site:trees:read', 'Read information about system site trees.'),
            $this->buildScope('system:info:read', 'Read detailed information about the system.'),
        ]; 
    }
}
