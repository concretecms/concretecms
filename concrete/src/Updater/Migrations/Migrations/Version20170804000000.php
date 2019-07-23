<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Entity\File\Image\Thumbnail\Type\Type as ThumbnailTypeEntity;
use Concrete\Core\Entity\File\Image\Thumbnail\Type\TypeFileSet as ThumbnailTypeFileSet;
use Concrete\Core\File\Image\Thumbnail\Type\Type;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20170804000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        $this->refreshEntities([
            ThumbnailTypeEntity::class,
            ThumbnailTypeFileSet::class,
        ]);

        $type = Type::getByHandle('file_manager_detail');
        if ($type) {
            /*
             * Fix issue where file manager detail thumbnails were being added without a height.
             *
             * @var \Concrete\Core\Entity\File\Image\Thumbnail\Type\Type $type
             */
            if (!$type->getHeight()) {
                $type->setHeight($type->getWidth());
                $em = $this->connection->getEntityManager();
                $em->persist($type);
                $em->flush();
            }
        }
    }
}
