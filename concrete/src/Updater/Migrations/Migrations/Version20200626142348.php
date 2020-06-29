<?php declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Entity\Board\Designer\CustomElement;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20200626142348 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {
        $this->refreshEntities([
            CustomElement::class,
        ]);
        $this->createSinglePage('/dashboard/boards/designer');
        $this->createSinglePage('/dashboard/boards/designer/choose_items');
    }

}
