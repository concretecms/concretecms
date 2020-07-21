<?php /** @noinspection PhpUnused */

namespace Concrete\Core\User\Search\Menu;

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
                t('Edit Properties'),
                [
                    'data-bulk-action-type' => 'dialog',
                    'data-bulk-action-title' => t('Properties'),
                    'data-bulk-action-url' => Url::to('/ccm/system/dialogs/user/bulk/properties'),
                    'data-bulk-action-dialog-width' => '630',
                    'data-bulk-action-dialog-height' => '450'
                ]
            )
        );

        $menu->addItem(
            new LinkItem(
                "#",
                t('Activate Users'),
                [
                    'data-bulk-action-type' => 'dialog',
                    'data-bulk-action-title' => t('Activate Users'),
                    'data-bulk-action-url' => Url::to('/ccm/system/dialogs/user/bulk/activate'),
                    'data-bulk-action-dialog-width' => '630',
                    'data-bulk-action-dialog-height' => '450'
                ]
            )
        );

        $menu->addItem(
            new LinkItem(
                "#",
                t('Deactivate Users'),
                [
                    'data-bulk-action-type' => 'dialog',
                    'data-bulk-action-title' => t('Deactivate Users'),
                    'data-bulk-action-url' => Url::to('/ccm/system/dialogs/user/bulk/deactivate'),
                    'data-bulk-action-dialog-width' => '630',
                    'data-bulk-action-dialog-height' => '450'
                ]
            )
        );

        $menu->addItem(
            new LinkItem(
                "#",
                t('Add to Group'),
                [
                    'data-bulk-action-type' => 'dialog',
                    'data-bulk-action-title' => t('Add to Group'),
                    'data-bulk-action-url' => Url::to('/ccm/system/dialogs/user/bulk/groupadd'),
                    'data-bulk-action-dialog-width' => '630',
                    'data-bulk-action-dialog-height' => '450'
                ]
            )
        );

        $menu->addItem(
            new LinkItem(
                "#",
                t('Remove From Group'),
                [
                    'data-bulk-action-type' => 'dialog',
                    'data-bulk-action-title' => t('Remove From Group'),
                    'data-bulk-action-url' => Url::to('/ccm/system/dialogs/user/bulk/groupremove'),
                    'data-bulk-action-dialog-width' => '630',
                    'data-bulk-action-dialog-height' => '450'
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
                    'data-bulk-action-url' => Url::to('/ccm/system/dialogs/user/bulk/delete'),
                    'data-bulk-action-dialog-width' => '630',
                    'data-bulk-action-dialog-height' => '450'
                ]
            )
        );

        return $menu;
    }

}
