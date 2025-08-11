<?php

declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

final class Version20220909200000 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {
        $this->createSinglePage(
            '/dashboard/system/basics/production_mode',
            'Production Mode',
            ['meta_keywords' => 'production, staging, site copy, development copy, local']
        );
    }

}
