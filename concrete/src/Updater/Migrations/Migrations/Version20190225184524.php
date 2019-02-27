<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Entity\OAuth\AccessToken;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Doctrine\DBAL\Schema\Schema;

class Version20190225184524 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeSchema()
     */
    public function upgradeSchema(Schema $schema)
    {
        if ($schema->hasTable('OAuth2AccessToken')) {
            $table = $schema->getTable('OAuth2AccessToken');
            $foreignKeys = $table->getForeignKeys();
            foreach ($foreignKeys as $foreignKey) {
                $localColumns = array_map('strtolower', $foreignKey->getLocalColumns());
                if (in_array('refreshtoken', $localColumns, true)) {
                    $table->removeForeignKey($foreignKey->getName());
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        $this->refreshEntities([
            AccessToken::class,
        ]);
    }
}
