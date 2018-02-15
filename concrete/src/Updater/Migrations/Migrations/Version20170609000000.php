<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\File\Image\Thumbnail\Type\Type;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20170609000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        $this->refreshEntities([
            'Concrete\Core\Entity\File\Image\Thumbnail\Type\Type',
        ]);
        $config = \Core::make('config');
        $type = Type::getByHandle($config->get('concrete.icons.file_manager_listing.handle'));
        /* @var \Concrete\Core\Entity\File\Image\Thumbnail\Type\Type $type */
        if ($type) {
            $type->setSizingMode($type::RESIZE_EXACT);
            $type->save();
        }
        $type = Type::getByHandle($config->get('concrete.icons.file_manager_detail.handle'));
        /* @var \Concrete\Core\Entity\File\Image\Thumbnail\Type\Type $type */
        if ($type) {
            $type->setSizingMode($type::RESIZE_EXACT);
            $type->save();
        }
    }
}
