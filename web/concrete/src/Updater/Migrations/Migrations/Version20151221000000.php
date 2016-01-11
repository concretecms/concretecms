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
        // image resizing options
        $sp = Page::getByPath('/dashboard/system/files/image_uploading');
        if (!is_object($sp) || $sp->isError()) {
            $sp = \Concrete\Core\Page\Single::add('/dashboard/system/files/image_uploading');
            $sp->update(array('cName' => 'Image Uploading'));
        }

        // background size/position
        \Concrete\Core\Database\Schema\Schema::refreshCoreXMLSchema(array(
            'StyleCustomizerInlineStyleSets',
        ));

        $bt = \BlockType::getByHandle('image_slider');
        if (is_object($bt)) {
            $bt->refresh();
        }

        $bt = \BlockType::getByHandle('youtube');
        if (is_object($bt)) {
            $bt->refresh();
        }

        $bt = \BlockType::getByHandle('autonav');
        if (is_object($bt)) {
            $bt->refresh();
        }




    }

    public function down(Schema $schema)
    {
    }


}
