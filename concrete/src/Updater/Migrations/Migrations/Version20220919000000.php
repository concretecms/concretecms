<?php

declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

final class Version20220919000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    public function upgradeDatabase()
    {
        $this->output(t('Updating tables found in doctrine xml...'));
        \Concrete\Core\Database\Schema\Schema::refreshCoreXMLSchema([
            'PageTypeComposerFormLayoutSets',
        ]);
    }
}
