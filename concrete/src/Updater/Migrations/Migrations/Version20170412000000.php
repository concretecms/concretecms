<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Page\Page;
use Concrete\Core\Page\Single as SinglePage;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20170412000000 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->refreshDatabaseTables([
            'FailedLoginAttempts',
            'LoginControlIpRanges',
        ]);
        $this->connection->executeQuery('DROP TABLE IF EXISTS SignupRequests');
        $this->connection->executeQuery('DROP TABLE IF EXISTS UserBannedIPs');

        // Add the new dashboard page to show IP ranges
        $sp = Page::getByPath('/dashboard/system/permissions/blacklist/range');
        if (!is_object($sp) || $sp->isError()) {
            $sp = SinglePage::add('/dashboard/system/permissions/blacklist/range');
            $sp->update(['cName' => 'IP Range']);
            $sp->setAttribute('exclude_nav', true);
        }
    }

    public function down(Schema $schema)
    {
    }
}
