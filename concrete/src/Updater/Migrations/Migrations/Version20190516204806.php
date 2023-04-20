<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20190516204806 extends AbstractMigration implements RepeatableMigrationInterface
{
    
    
    public function upgradeDatabase()
    {
        // No longer used. This used to use the ScopeRegistryInterface to repopulate the scopes table. Instead
        // we use the SynchronizeScopesCommand in another migration.
    }


}
