<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Page\Page;
use Concrete\Core\Page\Single as SinglePage;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20170404000000 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $timezone = \Config::get('app.timezone');
        if ($timezone) {
            // We have a legacy timezone we need to move into the site.
            $site = \Core::make('site')->getSite();
            $config = $site->getConfigRepository();
            $config->save('timezone', $timezone);
        }

        // Add the new dashboard page to update language files
        $sp = Page::getByPath('/dashboard/system/basics/multilingual/update');
        if (!is_object($sp) || $sp->isError()) {
            $sp = SinglePage::add('/dashboard/system/basics/multilingual/update');
            $sp->update(['cName' => 'Update Languages']);
            $sp->setAttribute('exclude_nav', true);
            $sp->setAttribute('meta_keywords', 'languages, update, gettext, translation');
        }
    }

    public function down(Schema $schema)
    {
    }
}
