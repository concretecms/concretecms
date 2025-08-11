<?php

namespace Concrete\Core\User\Group\Search\Menu;

use Concrete\Core\Application\UserInterface\ContextMenu\DropdownMenu;
use Concrete\Core\Application\UserInterface\ContextMenu\Item\LinkItem;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\User\Group\CanDeleteGroupsTrait;

class MenuFactory
{
    use CanDeleteGroupsTrait;

    public function createBulkMenu(): DropdownMenu
    {
        $menu = new DropdownMenu();
        if ($this->userCanDeleteGroups()) {
            $menu->addItem(
                new LinkItem(
                    'javascript:void(0)',
                    t('Delete'),
                    [
                        'data-bulk-action-type' => 'dialog',
                        'data-bulk-action-title' => t('Delete'),
                        'data-bulk-action-url' => Url::to('/ccm/system/dialogs/groups/bulk/delete'),
                        'data-bulk-action-dialog-width' => '630',
                        'data-bulk-action-dialog-height' => '450',
                    ]
                )
            );
        }

        return $menu;
    }
}
