<?php

declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Application\UserInterface\Dashboard\Navigation\NavigationCache;
use Concrete\Core\Attribute\Category\PageCategory;
use Concrete\Core\Page\Page;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Concrete\Core\Updater\Migrations\AbstractMigration;

final class Version20211020151701 extends AbstractMigration implements RepeatableMigrationInterface
{
    public function upgradeDatabase()
    {
        Page::getByPath('/dashboard/system/permissions/blacklist')->delete();
        Page::getByPath('/dashboard/system/permissions/blacklist/configure')->delete();
        Page::getByPath('/dashboard/system/permissions/blacklist/range')->delete();

        $this->createSinglePage('/dashboard/system/permissions/denylist', 'IP Deny List', [
            "meta_keywords" => "security, lock ip, lock out, block ip, address, restrict, access"
        ]);

        $configurePage = $this->createSinglePage('/dashboard/system/permissions/denylist/configure', 'Configure IP Blocking');

        if ($configurePage && !$configurePage->isError()) {
            if ($this->isAttributeHandleValid(PageCategory::class, 'exclude_nav')) {
                $configurePage->setAttribute('exclude_nav', true);
            }
        }

        $rangePage = $this->createSinglePage('/dashboard/system/permissions/denylist/range', 'IP Range');

        if ($rangePage && !$rangePage->isError()) {
            if ($this->isAttributeHandleValid(PageCategory::class, 'exclude_nav')) {
                $rangePage->setAttribute('exclude_nav', true);
            }
        }

        /** @var NavigationCache $navigationCache */
        $navigationCache = $this->app->make(NavigationCache::class);
        $navigationCache->clear();
    }
}
