<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Entity\OAuth\AccessToken;
use Concrete\Core\Entity\OAuth\RefreshToken;
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

        // Update all access tokens to be associated with their refresh tokens
        $tokens = $entityManager->createQueryBuilder()
            ->select('at,rt')
            ->from(RefreshToken::class, 'rt')
            ->join('rt.accessToken', 'at')
            ->getQuery()->execute();

        $count = 0;

        /** @var RefreshToken $token */
        foreach ($tokens as $token) {
            $accessToken = $entityManager->merge($token->getAccessToken());

            if (!$accessToken->getRefreshToken()) {
                $accessToken->setRefreshToken($token);
                $count++;
            }

            if ($count > 50) {
                $entityManager->flush();
                $count = 0;
            }
        }

        if ($count) {
            $entityManager->flush();
        }
    }
}
