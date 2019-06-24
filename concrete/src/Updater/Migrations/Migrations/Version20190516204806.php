<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Api\OAuth\Scope\ScopeRegistryInterface;
use Concrete\Core\Entity\OAuth\Scope;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20190516204806 extends AbstractMigration implements RepeatableMigrationInterface
{
    
    
    public function upgradeDatabase()
    {
        // delete all scopes.
        $this->connection->executeQuery('truncate table OAuth2Scope');
        
        // re-add them from the registry properly.
        $registry = $this->app->make(ScopeRegistryInterface::class);
        $em = $this->connection->getEntityManager();
        /**
         * @var $registry ScopeRegistryInterface
         */
        foreach($registry->getScopes() as $scope) {
            $existingScope = $em->find(Scope::class, $scope->getIdentifier());
            if (!$existingScope) {
                $em->persist($scope);
            }
        }
        $em->flush();
    }


}
