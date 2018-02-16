<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Page\Stack\StackList;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20150616000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        \Concrete\Core\Database\Schema\Schema::refreshCoreXMLSchema([
            'Stacks',
        ]);

        if (\Core::make('multilingual/detector')->isEnabled()) {
            StackList::rescanMultilingualStacks();
        }
    }
}
