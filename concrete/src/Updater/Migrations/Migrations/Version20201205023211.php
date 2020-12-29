<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

final class Version20201205023211 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {
        $this->refreshBlockType('date_navigation');
        $this->refreshBlockType('event_list');
        $this->refreshBlockType('express_entry_list');
        $this->refreshBlockType('feature');
        $this->refreshBlockType('page_list');
        $this->refreshBlockType('rss_displayer');
        $this->refreshBlockType('topic_list');
        $this->refreshBlockType('google_map');
    }

}
