<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\DirectSchemaUpgraderInterface;
use Doctrine\DBAL\Schema\Comparator;

class Version20140930000000 extends AbstractMigration implements DirectSchemaUpgraderInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Doctrine\DBAL\Migrations\AbstractMigration::getDescription()
     */
    public function getDescription()
    {
        return '5.7.0.4';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\DirectSchemaUpgraderInterface::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        \Database::query('UPDATE Config SET configNamespace="" WHERE configNamespace IS NULL');

        $config = $schema->getTable('Config');
        $fromConfig = clone $config;
        $db = \Database::get();
        $platform = $db->getDatabasePlatform();
        $config->dropPrimaryKey();
        $config->setPrimaryKey(['configNamespace', 'configGroup', 'configItem']);
        $comparator = new Comparator();
        $diff = $comparator->diffTable($fromConfig, $config);
        $sql = $platform->getAlterTableSQL($diff);
        if (is_array($sql) && count($sql)) {
            foreach ($sql as $q) {
                $db->query($q);
            }
        }
    }
}
