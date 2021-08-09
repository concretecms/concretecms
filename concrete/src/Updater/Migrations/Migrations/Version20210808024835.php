<?php

declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Page\Theme\Documentation\Installer;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

final class Version20210808024835 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {
        $this->createSinglePage(
            THEME_DOCUMENTATION_PAGE_PATH,
            'Theme Documentation',
            [
                'icon_dashboard' => 'fas fa-palette',
            ]
        );

        $elemental = Theme::getByHandle('elemental');
        $installer = $this->app->make(Installer::class);

        /**
         * @var Installer $installer
         */
        $installer->clearDocumentation($elemental);
        $installer->install($elemental, $elemental->getDocumentationProvider());
    }

}
