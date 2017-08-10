<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Single as SinglePage;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Attribute\Category\PageCategory;

class Version20170420000000 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $pageAttributeCategory = Application::getFacadeApplication()->make(PageCategory::class);
        /* @var PageCategory $pageAttributeCategory */
        $availableAttributes = [];
        foreach (['meta_keywords'] as $akHandle) {
            $availableAttributes[$akHandle] = $pageAttributeCategory->getAttributeKeyByHandle($akHandle) ? true : false;
        }

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
            if ($availableAttributes['meta_keywords']) {
                $sp->setAttribute('meta_keywords', 'upgrade, new version, update');
            }
        }
    }

    public function down(Schema $schema)
    {
    }
}
