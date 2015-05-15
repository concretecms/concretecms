<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use ORM;

class Version20150515000000 extends AbstractMigration
{

    public function getDescription()
    {
        return '5.7.5a1';
    }

    public function up(Schema $schema)
    {
        \Concrete\Core\Database\Schema\Schema::refreshCoreXMLSchema(array(
            'PageFeeds',
        ));

        // I can't seem to get the doctrine cache to clear any other way.
        $cms = \Core::make('app');
        $cms->clearCaches();
    }

    public function down(Schema $schema)
    {
    }
}
