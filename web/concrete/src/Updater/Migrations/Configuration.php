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
}
