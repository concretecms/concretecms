<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

/**
 * @since 8.2.0
 */
class Version20170421000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     * @since 8.3.2
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
