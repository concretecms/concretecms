<?php

declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Page\Page;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

final class Version20250227155410 extends AbstractMigration implements RepeatableMigrationInterface
{
    public function upgradeDatabase()
    {
        $this->refreshEntities([Site::class]);
        $this->createSinglePage(
            '/dashboard/system/basics/appearance',
            'Appearance',
            ['meta_keywords' => 'accessibility, appearance, dark mode, tooltips']
        );
        $accessibilityPage = Page::getByPath('/dashboard/system/basics/accessibility');
        if ($accessibilityPage && !$accessibilityPage->isError()) {
            $accessibilityPage->moveToTrash();
        }
        // Let's default the color scheme to "light" for backward compatibility
        $config = $this->app->make('config');
        $config->save('concrete.appearance.color_scheme', 'light');
    }
}
