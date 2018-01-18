<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\Routine\AddPageDraftsBooleanTrait;
use Doctrine\DBAL\Schema\Schema;

class Version20171218000000 extends AbstractMigration
{
    use AddPageDraftsBooleanTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addColumnIfMissing($schema);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }

    public function postUp(Schema $schema)
    {
        $this->migrateDrafts();
    }
}
