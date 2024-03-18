<?php

declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Entity\OAuth\Client;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

final class Version20240318000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    public function upgradeDatabase(): void
    {
        $this->refreshEntities([
            // Update varchar to text
            Client::class,
        ]);
    }
}
