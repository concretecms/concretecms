<?php
namespace Concrete\Core\Updater\Migrations;

use Doctrine\DBAL\Migrations\Configuration\Configuration as DoctrineMigrationConfiguration;

class Configuration extends DoctrineMigrationConfiguration
{
    public function __construct($registerMigrations = true)
    {
        $db = \Database::get();
        parent::__construct($db);
        $directory = DIR_BASE_CORE . '/' . DIRNAME_CLASSES . '/Updater/Migrations/Migrations';
        $this->setName(t('concrete5 Migrations'));
        $this->setMigrationsNamespace(('\Concrete\Core\Updater\Migrations\Migrations'));
        $this->setMigrationsDirectory($directory);
        if ($registerMigrations) {
            $this->registerMigrationsFromDirectory($directory);
        }
        $this->setMigrationsTableName('SystemDatabaseMigrations');
    }

    /*
     * This is a stupid requirement, but basically, we grab the lowest version number in our
     * system database migrations table, and we loop through all migrations in our file system
     * and for any of those LOWER than the lowest one in the table, we can assume they are included
     * in this migration. We then manually insert these rows into the SystemDatabaseMigrations table
     * so Doctrine isn't stupid and attempt to apply them.
     */
    public function registerPreviousMigratedVersions()
    {
        $db = \Database::get();
        try {

            $minimum = $db->GetOne('select min(version) from SystemDatabaseMigrations');
        } catch (\Exception $e) {
            return;
        }
        $migrations = $this->getMigrations();
        $keys = array_keys($migrations);

        if ($keys[0] == $minimum) {
            // This is the first migration in concrete5. That means we have already populated this table.
            return;
        } else {
            // We have to populate this table with all the migrations from the very first migration up to
            // the $minMigration
            foreach($migrations as $key => $migration) {
                if ($key < $minimum) {
                    $migration->markMigrated();
                }
            }
            // And now we have to reset the directory.
            $directory = DIR_BASE_CORE . '/' . DIRNAME_CLASSES . '/Updater/Migrations/Migrations';
            $this->registerMigrationsFromDirectory($directory);
        }
    }
}
