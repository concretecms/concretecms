<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\File\Image\Thumbnail\Type\Type;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20170609000000 extends AbstractMigration
{
    public function up(Schema $schema)
    {

        $this->refreshEntities([
            'Concrete\Core\Entity\File\Image\Thumbnail\Type\Type',
        ]);
        $config = \Core::make('config');
        $type = Type::getByHandle($config->get('concrete.icons.file_manager_listing.handle'));
        /**
         * @var $type \Concrete\Core\Entity\File\Image\Thumbnail\Type\Type
         */
        if ($type) {
            $type->setSizingMode($type::RESIZE_EXACT);
            $type->save();
        }
        $type = Type::getByHandle($config->get('concrete.icons.file_manager_detail.handle'));
        /**
         * @var $type \Concrete\Core\Entity\File\Image\Thumbnail\Type\Type
         */
        if ($type) {
            $type->setSizingMode($type::RESIZE_EXACT);
            $type->save();
        }
    }

    public function down(Schema $schema)
    {
    }
}
