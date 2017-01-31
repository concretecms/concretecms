<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Concrete\Core\Page\Page;
use SinglePage;


class Version20170131000000 extends AbstractMigration
{

    public function up(Schema $schema)
    {
        $sp = Page::getByPath('/dashboard/system/files/thumbnails/options');
        if (!is_object($sp) || $sp->isError()) {
            $sp = SinglePage::add('/dashboard/system/files/thumbnails/options');
            $sp->update(array('cName' => 'Thumbnail Options'));
            $sp->setAttribute('exclude_nav', true);
            $sp->setAttribute('meta_keywords', 'thumbnail, format, png, jpg, jpeg, quality, compression, gd, imagick, imagemagick, transparency');
        }
        
    }

    public function down(Schema $schema)
    {
    }
}
