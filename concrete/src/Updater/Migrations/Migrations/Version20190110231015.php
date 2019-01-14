<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Entity\OAuth\Client;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

/**
 * Update all oauth2 clients to have a consent type of CONSENT_SIMPLE
 */
class Version20190110231015 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {
        // Add the "consentType" field
        $this->refreshEntities([Client::class]);

        // Update the consent type for all existing clients
        $entityManager = $this->connection->createEntityManager();

        // Set all clients to use simple consent
        $qb = $entityManager->createQueryBuilder();
        $qb->update(Client::class, 'c')
            ->set('c.consentType', Client::CONSENT_SIMPLE)
            ->getQuery()->execute();
    }
}
