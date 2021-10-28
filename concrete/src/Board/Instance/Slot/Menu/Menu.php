<?php
namespace Concrete\Core\Board\Instance\Slot\Menu;

use Concrete\Core\Application\UserInterface\ContextMenu\Item\DialogLinkItem;
use Concrete\Core\Application\UserInterface\ContextMenu\Item\DividerItem;
use Concrete\Core\Application\UserInterface\ContextMenu\Item\LinkItem;
use Concrete\Core\Application\UserInterface\ContextMenu\PopoverMenu;
use Concrete\Core\Board\Instance\Slot\RenderedSlot;
use Concrete\Core\Permission\Checker;

class Menu extends PopoverMenu
{

    protected $menuAttributes = [
        'class' => 'ccm-edit-mode-block-menu',
    ];

    public function __construct(RenderedSlot $slot)
    {
        $instance = $slot->getInstance();
        $permissions = new Checker($instance);
        parent::__construct();
        $this->setAttribute('data-menu-board-instance-slot-id', $slot->getSlot());
        if ($permissions->canEditBoardInstanceSlot($slot->getSlot())) {
            $this->addItem(new LinkItem('javascript:void(0)', t('Pin To Board'), [
                'data-menu-action' => 'pin-item',
            ]));
            $this->addItem(new LinkItem('javascript:void(0)', t('Un-Pin From Board'), [
                'data-menu-action' => 'unpin-item',
            ]));
            $this->addItem(new LinkItem('javascript:void(0)', t('Delete Custom Slot'), [
                'data-menu-action' => 'delete-custom-slot',
            ]));
            $this->addItem(new LinkItem('javascript:void(0)', t('Replace Slot'), [
                'data-menu-action' => 'replace-slot',
            ]));
        }
    }

}