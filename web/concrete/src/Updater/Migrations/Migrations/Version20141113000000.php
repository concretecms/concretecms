<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Page\Page;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use SinglePage;
use Database;
use Concrete\Core\Block\BlockType\BlockType;

class Version20141113000000 extends AbstractMigration
{
    public function getDescription()
    {
        return '5.7.2.1';
    }

    public function up(Schema $schema)
    {

        /* delete customize page themes dashboard single page */
        $customize = Page::getByPath('/dashboard/pages/themes/customize');
        if (is_object($customize) && !$customize->isError()) {
            $customize->delete();
        }

        /* Add inspect single page back if it's missing */
        $sp = Page::getByPath('/dashboard/pages/themes/inspect');
        if (!is_object($sp) || $sp->isError()) {
            $sp = SinglePage::add('/dashboard/pages/themes/inspect');
            $sp->setAttribute('meta_keywords', 'inspect, templates');
            $sp->setAttribute('exclude_nav', 1);
        }

        $sp = Page::getByPath('/members/directory');
        if (!is_object($sp) || $sp->isError()) {
            $sp = SinglePage::add('/members/directory');
            $sp->setAttribute('exclude_nav', 1);
        }

        $bt = BlockType::getByHandle('feature');
        if (is_object($bt)) {
            $bt->refresh();
        }

        $bt = BlockType::getByHandle('image_slider');
        if (is_object($bt)) {
            $bt->refresh();
        }

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
            $db->Execute('delete from Files where fID = ?', array($row['fID']));
        }
    }

    public function down(Schema $schema)
    {
    }
}
