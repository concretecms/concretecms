<?php defined('C5_EXECUTE') or die("Access Denied.");

/**
 * @var $menu \Concrete\Core\Board\Instance\Slot\Menu\Menu
 * @var $slot \Concrete\Core\Entity\Board\InstanceSlot
 */
?>

<div class="ccm-ui"><?=$menu->getMenuElement()?></div>

<board-slot 
        :is-pinned="<?=$slot->isPinned() ? 'true' : 'false' ?>" 
        :instance-slot-id="<?=$slot->getBoardInstanceSlotID()?>">
