<?php

namespace Concrete\Core\Updater\Migrations;

use Doctrine\DBAL\Schema\Schema;

/**
 * Interface that migrations should implement when database downgrades are performed using only a DBAL Schema object.
 */
interface ManagedSchemaDowngraderInterface
{
    /**
     * Downgrade the database structure using ONLY the DBAL Schema object.
     *
     * @param Schema $schema
     */
    public function downgradeSchema(Schema $schema);
}
