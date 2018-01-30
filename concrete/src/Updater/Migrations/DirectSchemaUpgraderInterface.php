<?php

namespace Concrete\Core\Updater\Migrations;

/**
 * Interface that migrations should implement when database upgrades are performed directly on the database.
 */
interface DirectSchemaUpgraderInterface
{
    /**
     * Upgrade the database structure with direct database access.
     */
    public function upgradeDatabase();
}
