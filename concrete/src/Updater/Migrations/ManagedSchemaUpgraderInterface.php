<?php

namespace Concrete\Core\Updater\Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Interface that migrations should implement when database upgrades are performed using only a DBAL Schema object.
 */
interface ManagedSchemaUpgraderInterface
{
    /**
     * Upgrade the database structure using ONLY the DBAL Schema object.
     *
     * @param Schema $schema
     */
    public function upgradeSchema(Schema $schema);
}
