<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Attribute\Category\PageCategory;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Single as SinglePage;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\DirectSchemaUpgraderInterface;

class Version20170404000000 extends AbstractMigration implements DirectSchemaUpgraderInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\DirectSchemaUpgraderInterface::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        $pageAttributeCategory = Application::getFacadeApplication()->make(PageCategory::class);
        /* @var PageCategory $pageAttributeCategory */
        $availableAttributes = [];
        foreach ([
            'exclude_nav',
            'meta_keywords',
        ] as $akHandle) {
            $availableAttributes[$akHandle] = $pageAttributeCategory->getAttributeKeyByHandle($akHandle) ? true : false;
        }

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
            if ($availableAttributes['exclude_nav']) {
                $sp->setAttribute('exclude_nav', true);
            }
            if ($availableAttributes['meta_keywords']) {
                $sp->setAttribute('meta_keywords', 'languages, update, gettext, translation');
            }
        }
    }
}
