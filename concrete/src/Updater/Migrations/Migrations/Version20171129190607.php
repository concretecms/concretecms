<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Page\Single;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\DirectSchemaUpgraderInterface;

class Version20171129190607 extends AbstractMigration implements DirectSchemaUpgraderInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\DirectSchemaUpgraderInterface::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        $sp = \Page::getByPath('/dashboard/system/calendar/import');
        if (!is_object($sp) || $sp->isError()) {
            $sp = Single::add('/dashboard/system/calendar/import');
            $sp->update(['cName' => 'Import Calendar Data']);
        }
    }
}
