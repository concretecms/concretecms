<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Page\Single;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Concrete\Core\Updater\Migrations\Routine\AddPageDraftsBooleanTrait;

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
