<?php defined('C5_EXECUTE') or die("Access Denied.");

/**
 * @var $menu \Concrete\Core\Board\Instance\Slot\Menu\Menu
 * @var $slot \Concrete\Core\Board\Instance\Slot\RenderedSlot
 */
?>

<div class="ccm-ui"><?=$menu->getMenuElement()?></div>

<board-slot :slot-data='<?=json_encode($slot)?>'>
