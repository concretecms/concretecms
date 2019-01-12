<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Entity\OAuth\Scope;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

/**
 * Install OIDC scope
 */
class Version20190110194848 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {
        $em = $this->connection->getEntityManager();
        $scope = $em->find(Scope::class, 'openid');

        if ($scope) {
            // Scope already exists
            return;
        }

        $scope = new Scope();
        $scope->setIdentifier('openid');
        $scope->setDescription('OpenID Connect authentication flow');

        $em->persist($scope);
        $em->flush();
        $em->clear(Scope::class);
    }

    public function downgradeDatabase()
    {
        $em = $this->connection->getEntityManager();
        $scope = $em->find(Scope::class, 'openid');

        if ($scope) {
            $em->remove($scope);
        }
    }

}
