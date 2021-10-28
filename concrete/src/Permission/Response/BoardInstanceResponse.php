<?php
namespace Concrete\Core\Permission\Response;

use Concrete\Core\Board\Instance\Slot\RenderedSlotCollectionFactory;
use Concrete\Core\Permission\Checker;
use Doctrine\ORM\EntityManager;

class BoardInstanceResponse extends Response
{

    /**
     * Checks the current slot as well as the current time. If the slot is locked, it uses the canEditLockedRules
     * command, otherwise it just uses canEditBoardContents(). However in both cases if the timestamp of the rule
     * in the slot is in the future or ends in the past, we disregard the rule.
     *
     * @param int $slot
     * @return bool
     */
    public function canEditBoardInstanceSlot(int $slot): bool
    {
        $renderedSlotCollectionFactory = app(RenderedSlotCollectionFactory::class);
        $rules = $renderedSlotCollectionFactory->getCurrentRules($this->getPermissionObject());
        $permissionMethod = 'canEditBoardContents';
        foreach ($rules as $rule) {
            if ($rule->getSlot() == $slot && $rule->isLocked()) {
                $permissionMethod = 'canEditBoardLockedRules'; // One locked rule is all it takes.
            }
        }
        $boardPermissions = new Checker($this->getPermissionObject()->getBoard());
        return $boardPermissions->$permissionMethod();
    }


}
