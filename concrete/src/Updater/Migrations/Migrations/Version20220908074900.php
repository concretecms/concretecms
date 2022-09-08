<?php

declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Application\UserInterface\Dashboard\Navigation\NavigationCache;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

final class Version20220908074900 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        $this->createSinglePage(
            '/dashboard/system/files/uploads',
            'Upload Settings',
            [
                'meta_keywords' => 'files, upload, parallel, upload_max_filesize, post_max_size, limit, resize, chunk'
            ]
        );
        $navigationCache = $this->app->make(NavigationCache::class);
        $navigationCache->clear();
    }
}
