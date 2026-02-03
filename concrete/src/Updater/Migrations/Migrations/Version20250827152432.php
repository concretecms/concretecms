<?php

declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Page\Page;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

final class Version20250827152432 extends AbstractMigration implements RepeatableMigrationInterface
{
    public function upgradeDatabase()
    {
        $this->createSinglePage(
            '/dashboard/system/registration/logout',
            'Logout Options',
            ['meta_keywords' => 'login, logout, user, agent, ip, change, security, session, invalidation, invalid']
        );
        $logoutPage = Page::getByPath('/dashboard/system/registration/automated_logout');
        if ($logoutPage && !$logoutPage->isError()) {
            $logoutPage->moveToTrash();
        }
    }
}
