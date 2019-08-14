<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

/**
 * @since 8.2.0
 */
class Version20170404000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     * @since 8.3.2
     */
    public function upgradeDatabase()
    {
        $timezone = \Config::get('app.timezone');
        if ($timezone) {
            // We have a legacy timezone we need to move into the site.
            $site = \Core::make('site')->getSite();
            $config = $site->getConfigRepository();
            $config->save('timezone', $timezone);
        }

        // Add the new dashboard page to update language files
        $this->createSinglePage('/dashboard/system/basics/multilingual/update', 'Update Languages', ['exclude_nav' => true, 'meta_keywords' => 'languages, update, gettext, translation']);
    }
}
