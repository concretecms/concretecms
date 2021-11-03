<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Doctrine\DBAL\Schema\Schema;
use Concrete\Core\Updater\Migrations\AbstractMigration;

final class Version20210729191135 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {
        $db = $this->connection;
        $db->executeStatement('update BlockTypes set btHandle = "desktop_concrete_latest", btName = "Desktop Concrete Latest" where btHandle = "desktop_newsflow_latest"');
        if ($db->tableExists('btDesktopNewsflowLatest') && !$db->tableExists('btDesktopConcreteLatest')) {
            $this->connection->execute(sprintf('alter table %s rename %s', 'btDesktopNewsflowLatest', 'btDesktopConcreteLatest'));
        }
        $this->connection->executeStatement("update PermissionKeyCategories set pkCategoryHandle = 'marketplace' where pkCategoryHandle = 'marketplace_newsflow'");
        $db->executeStatement('update AuthenticationTypes set authTypeHandle = "external_concrete", authTypeName = "External Concrete" where authTypeHandle = "external_concrete5"');
        $db->executeStatement('update OauthUserMap set namespace = "external_concrete" where namespace="external_concrete5"');
    }
}
