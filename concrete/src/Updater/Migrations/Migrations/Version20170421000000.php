<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20170421000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        $this->refreshEntities([
            'Concrete\Core\Entity\Search\SavedUserSearch',
            'Concrete\Core\Entity\Search\SavedPageSearch',
            'Concrete\Core\Entity\Search\SavedFileSearch',
        ]);
        $this->refreshBlockType('image');
    }
}
