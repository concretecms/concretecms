<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Page\Type\Type;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20150615000000 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $type = Type::getByHandle(STACK_CATEGORY_PAGE_TYPE);
        if (!is_object($type)) {
            Type::add(array(
                'internal' => true,
                'name' => 'Stack Category',
                'handle' => STACK_CATEGORY_PAGE_TYPE,
            ));
        }
    }

    public function down(Schema $schema)
    {
    }
}
