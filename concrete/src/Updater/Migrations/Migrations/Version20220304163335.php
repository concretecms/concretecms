<?php

declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Application\UserInterface\Dashboard\Navigation\NavigationCache;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

final class Version20220304163335 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * @inheritDoc
     */
    public function upgradeDatabase()
    {
        $this->createSinglePage('/dashboard/system/environment/security', 'Security Policy', [
            'meta_keywords' => 'security, content security policy, csp, strict transport security, hsts, x-frame-options'
        ]);

        /** @var NavigationCache $navigationCache */
        $navigationCache = $this->app->make(NavigationCache::class);
        $navigationCache->clear();
    }
}
