<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Attribute\Category\PageCategory;
use Concrete\Core\Job\Job;
use Concrete\Core\Job\Set;
use Concrete\Core\Page\Page;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\DirectSchemaUpgraderInterface;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20180212000000 extends AbstractMigration implements DirectSchemaUpgraderInterface, RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\DirectSchemaUpgraderInterface::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        $job = Job::getByHandle('fill_thumbnails_table');
        if ($job === null) {
            Job::installByHandle('fill_thumbnails_table');
            $job = Job::getByHandle('fill_thumbnails_table');
            if ($job !== null) {
                $set = Set::getByName('Default');
                if ($set !== null) {
                    $set->addJob($job);
                }
            }
        }
        $sp = Page::getByPath('/dashboard/system/files/image_uploading');
        if (is_object($sp) && !$sp->isError()) {
            $sp->update([
                'cName' => 'Image Options',
            ]);
            if ($this->isAttributeHandleValid(PageCategory::class, 'meta_keywords')) {
                $sp->setAttribute('meta_keywords', 'uploading, upload, images, image, resizing, manager, exif, rotation, rotate, quality, compression, png, jpg, jpeg');
            }
        }
    }
}
