<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Page\Single;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171129190607 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $sp = \Page::getByPath('/dashboard/system/calendar/import');
        if (!is_object($sp) || $sp->isError()) {
            $sp = Single::add('/dashboard/system/calendar/import');
            $sp->update(['cName' => 'Import Calendar Data']);
        }

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
