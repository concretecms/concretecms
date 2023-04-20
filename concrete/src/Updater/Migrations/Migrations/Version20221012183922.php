<?php

declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\OAuth\Client;
use Concrete\Core\Entity\OAuth\Scope;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

final class Version20221012183922 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {
        $this->refreshEntities([
            Client::class,
            Entity::class,
            Scope::class,
        ]);
        $this->createSinglePage('/dashboard/system/api/scopes', 'Scopes');
    }


}
