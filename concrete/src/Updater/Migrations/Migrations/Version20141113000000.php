<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Page\Page;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Database;

class Version20141113000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Doctrine\DBAL\Migrations\AbstractMigration::getDescription()
     */
    public function getDescription()
    {
        return '5.7.2.1';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        /* delete customize page themes dashboard single page */
        $customize = Page::getByPath('/dashboard/pages/themes/customize');
        if (is_object($customize) && !$customize->isError()) {
            $customize->delete();
        }

        /* Add inspect single page back if it's missing */
        $this->createSinglePage('/dashboard/pages/themes/inspect', '', ['meta_keywords' => 'inspect, templates', 'exclude_nav' => 1]);

        $this->createSinglePage('/members/directory', '', ['exclude_nav' => 1]);

        $this->refreshBlockType('feature');

        $this->refreshBlockType('image_slider');

        $db = Database::get();
        $sm = $db->getSchemaManager();
        $schemaTables = $sm->listTableNames();
        if (in_array('signuprequests', $schemaTables)) {
            $db->query('alter table signuprequests rename SignupRequestsTmp');
            $db->query('alter table SignupRequestsTmp rename SignupRequests');
        }
        if (in_array('userbannedips', $schemaTables)) {
            $db->query('alter table userbannedips rename UserBannedIPsTmp');
            $db->query('alter table UserBannedIPsTmp rename UserBannedIPs');
        }

        // Clean up File stupidity
        $r = $db->Execute('select Files.fID from Files left join FileVersions on (Files.fID = FileVersions.fID) where FileVersions.fID is null');
        while ($row = $r->FetchRow()) {
            $db->Execute('delete from Files where fID = ?', [$row['fID']]);
        }
    }
}
