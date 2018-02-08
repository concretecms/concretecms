<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Entity\File\Version;
use Concrete\Core\Updater\Migrations\AbstractMigration;

class Version20180130000000 extends AbstractMigration
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        $this->refreshEntities([
            Version::class,
        ]);
    }
}
