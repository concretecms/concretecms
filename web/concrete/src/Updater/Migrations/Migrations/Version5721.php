<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Page\Page;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use SinglePage;

class Version5721 extends AbstractMigration
{

    public function getName()
    {
        return '20141028000000';
    }

    public function up(Schema $schema)
    {

        /** delete customize page themes dashboard single page */
        $customize = Page::getByPath('/dashboard/pages/themes/customize');
        if (is_object($customize) && !$customize->isError()) {
            $customize->delete();
        }

        /** Add inspect single page back if it's missing */
        $sp = Page::getByPath('/dashboard/pages/themes/inspect');
		if (!is_object($sp) || $sp->isError()) {
			$sp = SinglePage::add('/dashboard/pages/themes/inspect');
			$sp->setAttribute('meta_keywords', 'inspect, templates');
			$sp->setAttribute('exclude_nav', 1);
        }
    }

    public function down(Schema $schema)
    {
    }
}
