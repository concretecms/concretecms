<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Page\Page;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20150612000000 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $sp = Page::getByPath('/dashboard/system/multilingual/copy');
        if (!is_object($sp) || $sp->isError()) {
            $sp = \Concrete\Core\Page\Single::add('/dashboard/system/multilingual/copy');
            $sp->update(array('cName' => 'Copy Languages'));
        }
    }

    public function down(Schema $schema)
    {
    }
}
