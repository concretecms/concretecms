<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\File\Image\Thumbnail\Type\Type;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\DirectSchemaUpgraderInterface;

class Version20170804000000 extends AbstractMigration implements DirectSchemaUpgraderInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\DirectSchemaUpgraderInterface::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        $type = Type::getByHandle('file_manager_detail');
        if ($type) {
            /**
             * Fix issue where file manager detail thumbnails were being added without a height.
             *
             * @var \Concrete\Core\Entity\File\Image\Thumbnail\Type\Type
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
