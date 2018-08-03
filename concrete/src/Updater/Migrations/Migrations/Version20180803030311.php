<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Entity\OAuth\AccessToken;
use Concrete\Core\Entity\OAuth\AuthCode;
use Concrete\Core\Entity\OAuth\Client;
use Concrete\Core\Entity\OAuth\RefreshToken;
use Concrete\Core\Entity\OAuth\Scope;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180803030311 extends AbstractMigration
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
