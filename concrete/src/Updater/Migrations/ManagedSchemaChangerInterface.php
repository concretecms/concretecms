<?php

namespace Concrete\Core\Updater\Migrations;

/**
 * Interface that migrations should implement when database upgrades and downgrades are performed using only a DBAL Schema object.
 */
interface ManagedSchemaChangerInterface extends ManagedSchemaUpgraderInterface, ManagedSchemaDowngraderInterface
{
}
