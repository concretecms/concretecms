<?php

declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Apply to the tables of the block types the declarations of their fields, whose booleans are now
 * declared as such.
 */
final class Version20261002065000 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        foreach ([
            'accordion',
            'breadcrumbs',
            'calendar',
            'core_conversation',
            'document_library',
            'express_entry_list',
            'express_form',
            'form',
            'image',
            'image_slider',
            'next_previous',
            'page_list',
            'page_title',
            'search',
            'survey',
            'top_navigation_bar',
            'youtube',
        ] as $blockTypeHandle) {
            $this->refreshBlockType($blockTypeHandle);
        }
    }
}
