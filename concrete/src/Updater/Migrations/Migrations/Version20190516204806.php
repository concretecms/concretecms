<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20190516204806 extends AbstractMigration implements RepeatableMigrationInterface
{

    protected function renameScope($scope, $new)
    {
        $this->connection->update('OAuth2Scope', ['identifier' => $new], ['identifier' => $scope]);
    }

    protected function setScopeDescription($scope, $description)
    {
        $this->connection->update('OAuth2Scope', ['description' => $description], ['identifier' => $scope]);
    }

    protected function addScope($scope)
    {
        $existingScope = $this->connection->fetchColumn('select identifier from OAuth2Scope where identifier = ?', [
            $scope
        ]);
        if (!$existingScope) {
            $this->connection->insert('OAuth2Scope', ['identifier' => $scope, 'description' => '']);
        }
    }

    public function upgradeDatabase()
    {
        // rename the scopes into what they are now.
        $this->renameScope('system', 'system:info:read');
        $this->renameScope('account', 'account:read');
        $this->renameScope('site', 'site:trees:read');

        // add the new scopes.
        $this->addScope('files:read');

        // set the descriptions
        $config = $this->app->make('config');
        $scopeData = $config->get('app.api.scopes');

        $this->setScopeDescription('system:info:read', $scopeData['system:info:read']);
        $this->setScopeDescription('account:read', $scopeData['account:read']);
        $this->setScopeDescription('site:trees:read', $scopeData['site:trees:read']);
        $this->setScopeDescription('files:read', $scopeData['files:read']);
        $this->setScopeDescription('openid', $scopeData['openid']);
    }


}
