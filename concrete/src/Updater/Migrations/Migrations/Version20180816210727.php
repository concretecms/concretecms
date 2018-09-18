<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Entity\OAuth\AccessToken;
use Concrete\Core\Entity\OAuth\AuthCode;
use Concrete\Core\Entity\OAuth\Client;
use Concrete\Core\Entity\OAuth\RefreshToken;
use Concrete\Core\Entity\OAuth\Scope;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20180816210727 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {
        $this->refreshEntities([
            AccessToken::class,
            AuthCode::class,
            Client::class,
            RefreshToken::class,
            Scope::class
        ]);
        $this->createSinglePage('/dashboard/system/api', 'API');
        $this->createSinglePage('/dashboard/system/api/settings', 'API Settings');
        $this->createSinglePage('/dashboard/system/api/integrations', 'API Integrations', ['exclude_nav' => true, 'exclude_search_index' => true]);
    }
}
