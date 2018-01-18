<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\DirectSchemaUpgraderInterface;

class Version20150610000000 extends AbstractMigration implements DirectSchemaUpgraderInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\DirectSchemaUpgraderInterface::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        $bt = \BlockType::getByHandle('file');
        if (is_object($bt)) {
            $bt->refresh();
        }
        if (\Config::get('conversation.banned_words')) {
            \Config::set('conversations.banned_words', true);
        }
    }
}
