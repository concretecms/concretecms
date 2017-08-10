<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\File\Image\Thumbnail\Type\Type;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20170804000000 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $type = Type::getByHandle('file_manager_detail');
        if ($type) {
            /**
             * Fix issue where file manager detail thumbnails were being added without a height.
             * @var $type \Concrete\Core\Entity\File\Image\Thumbnail\Type\Type
             */
            if (!$type->getHeight()) {
                $type->setHeight($type->getWidth());
                $em = $this->connection->getEntityManager();
                $em->persist($type);
                $em->flush();
            }
        }
    }

    public function down(Schema $schema)
    {
    }
}
