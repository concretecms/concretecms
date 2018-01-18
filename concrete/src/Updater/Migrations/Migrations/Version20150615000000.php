<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Page\Type\Type;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\DirectSchemaUpgraderInterface;

class Version20150615000000 extends AbstractMigration implements DirectSchemaUpgraderInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\DirectSchemaUpgraderInterface::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        $type = Type::getByHandle(STACK_CATEGORY_PAGE_TYPE);
        if (!is_object($type)) {
            Type::add([
                'internal' => true,
                'name' => 'Stack Category',
                'handle' => STACK_CATEGORY_PAGE_TYPE,
            ]);
        }
    }
}
