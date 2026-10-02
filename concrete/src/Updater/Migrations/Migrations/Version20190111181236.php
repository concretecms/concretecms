<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Entity\OAuth\AccessToken;
use Concrete\Core\Entity\OAuth\Scope;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

/**
 * Update scope descriptions and connect access tokens to refresh tokens
 */
class Version20190111181236 extends AbstractMigration implements RepeatableMigrationInterface
{

    private function getScopeDescription($key)
    {
        $map = [
            'account' => t('General user account information'),
            'openid' => t('User profile information for authentication'),
            'site' => t('Site configuration'),
            'system' => t('System configuration'),
        ];

        return isset($map[$key]) ? $map[$key] : null;
    }

    public function upgradeDatabase()
    {
        // Update the consent type for all existing clients
        $entityManager = $this->connection->createEntityManager();

        $scopeRepository = $entityManager->getRepository(Scope::class);
        $scopes = $scopeRepository->findAll();

        /** @var Scope $scope */
        foreach ($scopes as $scope) {
            $newDescription = $this->getScopeDescription($scope->getIdentifier());
            if ($newDescription) {
                $scope->setDescription($newDescription);
            }
        }

        $entityManager->flush();

        // Refresh the access token entity
        $this->refreshEntities([AccessToken::class]);
        // The association between the access tokens and their refresh tokens (that this migration used to populate) was removed shortly after
    }
}
