<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Permission\Access\Entity\Type;
use Concrete\Core\Permission\Category;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use \Concrete\Core\Conversation\FlagType\FlagType;
use Concrete\Core\Block\BlockType\BlockType;

class Version5722 extends AbstractMigration
{

    public function getName()
    {
        return '20141126000000';
    }

    public function up(Schema $schema)
    {
        $ft = FlagType::getByhandle('spam');
        if (!is_object($ft)) {
            FlagType::add('spam');
        }

        $bt = BlockType::getByHandle('image_slider');
        $bt->refresh();

        $types = array(Type::getByHandle('group'), Type::getByHandle('user'), Type::getByHandle('group_set'), Type::getByHandle('group_combination'));
        $categories = array(Category::getByHandle('conversation'), Category::getByHandle('conversation_message'));
        foreach($categories as $category) {
            foreach($types as $pe) {
                if (is_object($category) && is_object($pe)) {
                    $category->associateAccessEntityType($pe);
                }
            }
        }
    }

    public function down(Schema $schema)
    {
    }
}
