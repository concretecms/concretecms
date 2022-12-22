<?php

declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Doctrine\DBAL\Schema\Schema;
use Concrete\Core\Updater\Migrations\AbstractMigration;

final class Version20221221224709 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {
        $this->createSinglePage(
            '/dashboard/system/basics/site_information',
            'Site Information',
            ['exclude_search_index' => true]
        );
    }
}
