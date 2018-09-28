<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Entity\File\Image\Thumbnail\Type\Type;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20180531000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    public function upgradeDatabase()
    {
        $this->refreshEntities([
            Type::class,
        ]);
    }
}
