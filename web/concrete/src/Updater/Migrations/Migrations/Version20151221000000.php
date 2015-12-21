<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Page\Page;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use ORM;

class Version20151221000000 extends AbstractMigration
{

    public function up(Schema $schema)
    {
        $sp = Page::getByPath('/dashboard/system/files/image_uploading');
        if (!is_object($sp) || $sp->isError()) {
            $sp = \Concrete\Core\Page\Single::add('/dashboard/system/files/image_uploading');
            $sp->update(array('cName' => 'Image Uploading'));
        }
    }

    public function down(Schema $schema)
    {
    }


}
