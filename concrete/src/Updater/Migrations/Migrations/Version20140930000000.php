<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Doctrine\DBAL\Schema\Schema;

class Version20140930000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Doctrine\Migrations\AbstractMigration::getDescription()
     */
    public function getDescription(): string
    {
        return '5.7.0.4';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Doctrine\Migrations\AbstractMigration::preUp()
     */
    public function preUp(Schema $schema): void
    {
        \Database::query('UPDATE Config SET configNamespace="" WHERE configNamespace IS NULL');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeSchema()
     */
    public function upgradeSchema(Schema $schema)
    {
        $config = $schema->getTable('Config');
        $config->dropPrimaryKey();
        $config->setPrimaryKey(['configNamespace', 'configGroup', 'configItem']);
    }
}
