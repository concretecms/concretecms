<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Comparator;
use Doctrine\DBAL\Schema\Schema;

class Version5704 extends AbstractMigration
{

    public function getName()
    {
        return '20140930000000';
    }

    public function up(Schema $schema)
    {
        try {
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
        } catch(\Exception $e) {

        }
    }

    public function down(Schema $schema)
    {
    }
}
