<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Entity\OAuth\Scope;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

/**
 * Update scope descriptions and install missing scopes on upgrades.
 */
class Version20190129000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    /** @var array Mapping scope identifiers to descriptions */
    protected $mapping = [];

    private function needsUpdating($key, $value)
    {
        if (!isset($this->mapping[$key])) {
            return false;
        }

        if ((string) $value == (string) $this->mapping[$key]) {
            return false;
        }

        return true;
    }

    public function upgradeDatabase()
    {
        // Set the current mapping of api scopes
        $this->mapping = [
            'account' => t('General user account information'),
            'openid' => t('User profile information for authentication'),
            'site' => t('Site configuration'),
            'system' => t('System configuration'),
        ];

        $entityManager = $this->connection->createEntityManager();

        $scopeRepository = $entityManager->getRepository(Scope::class);

        foreach ($this->mapping as $scopeID => $scopeDescription) {
            // Find existing scope
            $scope = $scopeRepository->findOneByIdentifier($scopeID);
            // Create a new scope if it doesnt exist
            if (!is_object($scope)) {
                $scope = new Scope();
                $scope->setIdentifier($scopeID);
                $scope->setDescription($scopeDescription);
            } elseif ($this->needsUpdating($scope->getIdentifier(), $scope->getDescription())) {
                $scope->setDescription($scopeDescription);
            }
            // Persist any changes to the entity
            $entityManager->persist($scope);
        }
        $entityManager->flush();
        $entityManager->clear(Scope::class);
    }
}
