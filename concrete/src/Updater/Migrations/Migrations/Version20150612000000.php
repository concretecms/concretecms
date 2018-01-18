<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Page\Page;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\DirectSchemaUpgraderInterface;

class Version20150612000000 extends AbstractMigration implements DirectSchemaUpgraderInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\DirectSchemaUpgraderInterface::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        $sp = Page::getByPath('/dashboard/system/multilingual/copy');
        if (!is_object($sp) || $sp->isError()) {
            $sp = \Concrete\Core\Page\Single::add('/dashboard/system/multilingual/copy');
            $sp->update(['cName' => 'Copy Languages']);
        }
    }
}
