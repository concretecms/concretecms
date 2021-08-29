<?php

declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Entity\Page\Feed;
use Concrete\Core\Page\Theme\Documentation\Installer;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

final class Version20210815000000 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {

        $this->refreshEntities([
            Feed::class,
        ]);
        $this->refreshBlockType('page_list');

        $bt = BlockType::getByHandle('core_theme_documentation_toc');
        if (!is_object($bt)) {
            BlockType::installBlockType('core_theme_documentation_toc');
        }
        $bt = BlockType::getByHandle('core_theme_documentation_breadcrumb');
        if (!is_object($bt)) {
            BlockType::installBlockType('core_theme_documentation_breadcrumb');
        }
        $bt = BlockType::getByHandle('top_navigation_bar');
        if (!is_object($bt)) {
            BlockType::installBlockType('top_navigation_bar');
        }
        $bt = BlockType::getByHandle('hero_image');
        if (!is_object($bt)) {
            BlockType::installBlockType('hero_image');
        }
        $bt = BlockType::getByHandle('accordion');
        if (!is_object($bt)) {
            BlockType::installBlockType('accordion');
        }
        $bt = BlockType::getByHandle('feature_link');
        if (!is_object($bt)) {
            BlockType::installBlockType('feature_link');
        }


        $this->createSinglePage(
            THEME_DOCUMENTATION_PAGE_PATH,
            'Theme Documentation',
            [
                'icon_dashboard' => 'fas fa-palette',
            ]
        );

        $atomik = Theme::getByHandle('atomik');
        if (!$atomik) {
            $atomik = Theme::add('atomik');
        }
        $elemental = Theme::getByHandle('elemental');
        $installer = $this->app->make(Installer::class);

        /**
         * @var Installer $installer
         */
        $installer->clearDocumentation($elemental);
        $installer->install($elemental, $elemental->getDocumentationProvider());

        $installer->clearDocumentation($atomik);
        $installer->install($elemental, $atomik->getDocumentationProvider());


    }

}
