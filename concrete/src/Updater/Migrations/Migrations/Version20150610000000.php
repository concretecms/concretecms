<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20150610000000 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $bt = \BlockType::getByHandle('file');
        if (is_object($bt)) {
            $bt->refresh();
        }
        if (\Config::get('conversation.banned_words')) {
            \Config::set('conversations.banned_words', true);
        }
    }

    public function down(Schema $schema)
    {
    }
}
