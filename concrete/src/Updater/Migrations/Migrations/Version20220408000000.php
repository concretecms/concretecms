<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Site\Service;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20220408000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * Web Spell Checker ended support on December 31st, 2011, and was removed from CKEditor4 since 4.18.0
     *
     * @return void
     */
    public function upgradeDatabase()
    {
        /** @var Service $site */
        $site = $this->app->make(Service::class);
        $sites = $site->getList();
        foreach ($sites as $site) {
            $config = $site->getConfigRepository();
            if ($config->has('editor.ckeditor4.plugins.selected')) {
                $selected = $config->get('editor.ckeditor4.plugins.selected', []);
                if (($key = array_search('wsc', $selected)) !== false) {
                    unset($selected[$key]);
                    $config->save('editor.ckeditor4.plugins.selected', $selected);
                }
            }
        }
    }
}
