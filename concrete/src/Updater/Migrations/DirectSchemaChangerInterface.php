<?php

namespace Concrete\Core\Updater\Migrations;

/**
 * Interface that migrations should implement when database upgrades and downgrades are performed directly on the database.
 */
interface DirectSchemaChangerInterface extends DirectSchemaUpgraderInterface, DirectSchemaDowngraderInterface
{
}
