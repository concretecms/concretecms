<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20181105102800 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        $this->output(t('Refreshing table FileImageThumbnailPaths...'));
        \Concrete\Core\Database\Schema\Schema::refreshCoreXMLSchema([
            'FileImageThumbnailPaths',
        ]);
    }
}
