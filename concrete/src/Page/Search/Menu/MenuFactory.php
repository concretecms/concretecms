<?php

namespace Concrete\Core\Page\Search\Menu;

use Concrete\Core\Application\UserInterface\ContextMenu\DropdownMenu;
use Concrete\Core\Application\UserInterface\ContextMenu\Item\DividerItem;
use Concrete\Core\Application\UserInterface\ContextMenu\Item\LinkItem;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Support\Facade\Url;

class MenuFactory
{

    protected $config;

    public function __construct(
        Repository $config
    )
    {
        $this->config = $config;
    }

    /** @noinspection PhpUnused */
    public function createBulkMenu(): DropdownMenu
    {
        $menu = new DropdownMenu();

        $menu->addItem(
            new LinkItem(
                "#",
                t('Move/Copy'),
                [
                    'data-bulk-action' => 'move-copy'
                ]
            )
        );

        $menu->addItem(
            new LinkItem(
                "#",
                t('Delete'),
                [
                    'data-bulk-action-type' => 'dialog',
                    'data-bulk-action-title' => t('Delete'),
                    'data-bulk-action-url' => Url::to('/ccm/system/dialogs/page/bulk/delete'),
                    'data-bulk-action-dialog-width' => '630',
                    'data-bulk-action-dialog-height' => '450'
                ]
            )
        );

        $menu->addItem(
            new LinkItem(
                "#",
                t('Properties'),
                [
                    'data-bulk-action-type' => 'dialog',
                    'data-bulk-action-title' => t('Properties'),
                    'data-bulk-action-url' => Url::to('/ccm/system/dialogs/page/bulk/properties'),
                    'data-bulk-action-dialog-width' => '630',
                    'data-bulk-action-dialog-height' => '450'
                ]
            )
        );


        if ($this->config->get('concrete.permissions.model') !== 'simple') {
            $menu->addItem(new DividerItem());

            $menu->addItem(
                new LinkItem(
                    "#",
                    t('Change Permissions'),
                    [
                        'data-bulk-action-type' => 'dialog',
                        'data-bulk-action-title' => t('Page Permissions'),
                        'data-bulk-action-url' => Url::to('/ccm/system/dialogs/page/bulk/permissions'),
                        'data-bulk-action-dialog-width' => '630',
                        'data-bulk-action-dialog-height' => '450'
                    ]
                )
            );

            $menu->addItem(
                new LinkItem(
                    "#",
                    t('Change Permissions - Add Access'),
                    [
                        'data-bulk-action-type' => 'dialog',
                        'data-bulk-action-title' => t('Page Permissions'),
                        'data-bulk-action-url' => Url::to('/ccm/system/dialogs/page/bulk/permissions/add_access'),
                        'data-bulk-action-dialog-width' => '630',
                        'data-bulk-action-dialog-height' => '450'
                    ]
                )
            );

            $menu->addItem(
                new LinkItem(
                    "#",
                    t('Change Permissions - Remove Access'),
                    [
                        'data-bulk-action-type' => 'dialog',
                        'data-bulk-action-title' => t('Page Permissions'),
                        'data-bulk-action-url' => Url::to('/ccm/system/dialogs/page/bulk/permissions/remove_access'),
                        'data-bulk-action-dialog-width' => '630',
                        'data-bulk-action-dialog-height' => '450'
                    ]
                )
            );
        }

        return $menu;
    }

}
