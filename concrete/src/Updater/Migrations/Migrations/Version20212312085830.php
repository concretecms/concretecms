<?php declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Doctrine\DBAL\Schema\Schema;

final class Version20212312085830 extends AbstractMigration  implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {
        $this->refreshBlockType('accordion');
    }
}
