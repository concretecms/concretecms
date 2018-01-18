<?php

namespace Concrete\Core\Updater\Migrations;

/**
 * Interface that migrations should implement when database downgrades are performed directly on the database.
 */
interface DirectSchemaDowngraderInterface
{
    /**
     * Downgrade the database structure with direct database access.
     */
    public function downgradeDatabase();
}
