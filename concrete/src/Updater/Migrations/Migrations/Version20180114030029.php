<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20180114030029 extends AbstractMigration
{
    public function upgradeDatabase()
    {
        $this->refreshDatabaseTables([
            'OAuthServerAccessTokens',
            'OAuthServerAuthorizationCodes',
            'OAuthServerClients',
            'OAuthServerJti',
            'OAuthServerJwt',
            'OAuthServerPublicKeys',
            'OAuthServerRefreshTokens',
            'OAuthServerScopes',
            'OAuthServerUsers'
        ]);
        $pk = Key::getByHandle('access_api');
        if (!$pk instanceof Key) {
            Key::add('admin', 'access_api', 'Access API', '', false, false);
        }
    }

}
