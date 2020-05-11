<?php
namespace Concrete\Core\Board\Instance\Slot\Menu;

use Concrete\Core\Application\UserInterface\ContextMenu\Item\LinkItem;
use Concrete\Core\Application\UserInterface\ContextMenu\PopoverMenu;
use Concrete\Core\Entity\Board\InstanceSlot;
use Concrete\Core\Permission\Checker;

class Menu extends PopoverMenu
{

    protected $menuAttributes = [
        'class' => 'ccm-edit-mode-block-menu',
    ];

    public function __construct(InstanceSlot $slot)
    {
        $board = $slot->getInstance()->getBoard();
        $permissions = new Checker($board);
        parent::__construct();
        $this->setAttribute('data-menu-board-instance-slot-id', $slot->getBoardInstanceSlotID());
        if ($permissions->canEditBoardContents()) {
            $this->addItem(new LinkItem('javascript:void(0)', t('Pin To Board'), [
                'data-menu-action' => 'pin_item',
            ]));
        }
    }

}
