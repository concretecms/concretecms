<?php

declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Page\Page;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

final class Version20241204161559 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        $page = Page::getByPath('/dashboard/system/basics/social');
        if ($page && !$page->isError()) {
            $page->delete();
        }
        $this->createSinglePage('/dashboard/system/social', 'Social &amp; Sharing');
        $this->createSinglePage('/dashboard/system/social/social_links', 'Social Links', ['meta_keywords' => 'sharing, facebook, twitter']);
        $this->createSinglePage('/dashboard/system/social/opengraph', 'Open Graph', ['meta_keywords' => 'sharing, opengraph, schema']);
    }
}
