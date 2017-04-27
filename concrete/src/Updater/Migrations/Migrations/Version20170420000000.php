<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Single as SinglePage;

class Version20170420000000 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        Page::getByPath('/dashboard/system/backup')->delete();
        Page::getByPath('/dashboard/system/backup/backup')->delete();
        Page::getByPath('/dashboard/system/backup/update')->delete();

        $page = Page::getByPath('/dashboard/system/update');
        if (!is_object($page) || $page->isError()) {
            $sp = SinglePage::add('/dashboard/system/update');
            $sp->update(array('cName' => 'Update concrete5'));
        }

        $page = Page::getByPath('/dashboard/system/update/update');
        if (!is_object($page) || $page->isError()) {
            $sp = SinglePage::add('/dashboard/system/update/update');
            $sp->update(array('cName' => 'Apply Update'));
            $sp->setAttribute('meta_keywords', 'upgrade, new version, update');
        }
    }

    public function down(Schema $schema)
    {
    }
}
