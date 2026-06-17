<?php

declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Application\UserInterface\Dashboard\Navigation\NavigationCache;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

final class Version20241126174022 extends AbstractMigration implements RepeatableMigrationInterface
{
    public function upgradeDatabase()
    {
        // Migrate the existing config
        $config = app('config');
        if ($config->get('concrete.debug.display_errors') === true) {
            if ($config->get('concrete.debug.detail') === 'debug') {
                $config->save('concrete.error.display.guests', 'debug');
                $config->save('concrete.error.display.privileged', 'debug');
            } else {
                $config->save('concrete.error.display.guests', 'message');
                $config->save('concrete.error.display.privileged', 'message');
            }
        } elseif ($config->get('concrete.debug.display_errors') === false) {
            $config->save('concrete.error.display.guests', 'generic');
            $config->save('concrete.error.display.privileged', 'generic');
        }
        $config->save('concrete.debug', null); // clear it

        $page = Page::getByPath('/dashboard/system/environment/debug');
        if ($page && !$page->isError()) {
            $page->delete();
        }
        $this->createSinglePage('/dashboard/system/environment/errors', 'Error Handling',
            [
                'meta_keywords' => 'error, exception, debug',
            ]
        );
        $pk = Key::getByHandle('view_debug_error_information');
        if (!$pk instanceof Key) {
            Key::add('admin', 'view_debug_error_information', 'View Debug Error Information', '', false, false);
        }
        $navigationCache = $this->app->make(NavigationCache::class);
        $navigationCache->clear();
    }
}
