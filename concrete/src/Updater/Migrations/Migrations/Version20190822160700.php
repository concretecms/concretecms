<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Type;

class Version20190822160700 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeSchema()
     */
    public function upgradeSchema(Schema $schema)
    {
        $this->fixCookieTable($schema);
    }

    protected function fixCookieTable(Schema $schema)
    {
        if (!$schema->hasTable('authTypeConcreteCookieMap')) {
            return;
        }
        $table = $schema->getTable('authTypeConcreteCookieMap');
        if (!$table->hasColumn('token')) {
            return;
        }
        $column = $table->getColumn('token');
        if ($column->getType()->getName() !== Type::STRING) {
            return;
        }
        if ($column->getLength() >= 64) {
            return;
        }
        $column->setLength(64);
    }
}
