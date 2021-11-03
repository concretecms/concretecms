<?php

declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Database\Schema\Schema;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

final class Version20211103142609 extends AbstractMigration implements RepeatableMigrationInterface
{
    public function upgradeDatabase()
    {
        $this->output(t('Updating tables found in doctrine xml...'));
        Schema::refreshCoreXMLSchema([
            'CollectionVersions',
            'Pages'
        ]);
    }
}
