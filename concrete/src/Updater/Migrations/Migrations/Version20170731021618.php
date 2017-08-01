<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Entity\Express\Entity;
use Doctrine\DBAL\Schema\Schema;
use Concrete\Core\Updater\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170731021618 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->refreshEntities([
            Entity::class
        ]);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
