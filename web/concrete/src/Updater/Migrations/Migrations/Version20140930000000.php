<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Comparator;
use Doctrine\DBAL\Schema\Schema;

class Version20140930000000 extends AbstractMigration
{
    public function getDescription()
    {
        return '5.7.0.4';
    }

    public function up(Schema $schema)
    {
        \Database::query('UPDATE Config SET configNamespace="" WHERE configNamespace IS NULL');

        $config = $schema->getTable('Config');
        $fromConfig = clone $config;
        $db = \Database::get();
        $platform = $db->getDatabasePlatform();
        $config->dropPrimaryKey();
        $config->setPrimaryKey(array('configNamespace', 'configGroup', 'configItem'));
        $comparator = new Comparator();
        $diff = $comparator->diffTable($fromConfig, $config);
        $sql = $platform->getAlterTableSQL($diff);
        if (is_array($sql) && count($sql)) {
            foreach ($sql as $q) {
                $db->query($q);
            }
        }
    }

    public function down(Schema $schema)
    {
    }
}
