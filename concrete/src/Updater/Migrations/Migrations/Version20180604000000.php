<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\File\Image\Thumbnail\Type\Type;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20180604000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    public function upgradeDatabase()
    {
        $type = Type::getByHandle('file_manager_listing');
        if ($type !== null) {
            $type->setIsUpscalingEnabled(true);
            $em = $this->connection->getEntityManager();
            $em->persist($type);
            $em->flush($type);
        }
    }
}
