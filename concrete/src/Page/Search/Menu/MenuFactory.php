<?php

namespace Concrete\Core\Page\Search\Menu;

use Concrete\Core\Application\UserInterface\ContextMenu\DropdownMenu;
use Concrete\Core\Application\UserInterface\ContextMenu\Item\LinkItem;
use Concrete\Core\Support\Facade\Url;

class MenuFactory
{

    public function createBulkMenu(): DropdownMenu
    {
        $menu = new DropdownMenu();

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

        return $menu;
    }

}
