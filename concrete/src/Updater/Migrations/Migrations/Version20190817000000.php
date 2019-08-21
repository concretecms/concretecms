<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Entity\Page\Feed;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20190817000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgradeDatabase()
    {
        $this->refreshEntities([
            Feed::class,
        ]);
    }
}
