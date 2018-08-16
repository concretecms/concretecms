<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Entity\OAuth\AccessToken;
use Concrete\Core\Entity\OAuth\AuthCode;
use Concrete\Core\Entity\OAuth\Client;
use Concrete\Core\Entity\OAuth\RefreshToken;
use Concrete\Core\Entity\OAuth\Scope;
use Concrete\Core\Updater\Migrations\AbstractMigration;

class Version20180816210727 extends AbstractMigration
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
    }
}
