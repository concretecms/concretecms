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

    }

    public function down(Schema $schema)
    {
    }


}
