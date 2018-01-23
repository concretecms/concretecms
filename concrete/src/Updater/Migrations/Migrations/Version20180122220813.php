<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Page\Page;
use Concrete\Core\Page\Single as SinglePage;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\DirectSchemaUpgraderInterface;

class Version20180122220813 extends AbstractMigration implements DirectSchemaUpgraderInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\DirectSchemaUpgraderInterface::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        $page = Page::getByPath('/dashboard/system/mail/addresses');
        if (!is_object($page) || $page->isError()) {
            $sp = SinglePage::add('/dashboard/system/mail/addresses');
            $sp->update(['cName' => 'System Email Addresses']);
            $sp->setAttribute('meta_keywords', 'mail settings, mail configuration, email, sender');
        }
    }
}
