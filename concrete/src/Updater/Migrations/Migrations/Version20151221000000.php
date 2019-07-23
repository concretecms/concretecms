<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20151221000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Doctrine\DBAL\Migrations\AbstractMigration::getDescription()
     */
    public function getDescription()
    {
        return '5.7.5.4';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        // image resizing options
        $this->createSinglePage('/dashboard/system/files/image_uploading', 'Image Uploading');

        // background size/position
        \Concrete\Core\Database\Schema\Schema::refreshCoreXMLSchema([
            'StyleCustomizerInlineStyleSets',
        ]);

        $this->refreshBlockType('image_slider');

        $this->refreshBlockType('youtube');

        $this->refreshBlockType('autonav');
    }
}
