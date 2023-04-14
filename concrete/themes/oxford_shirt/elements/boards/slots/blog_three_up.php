<?php 

defined('C5_EXECUTE') or die("Access Denied.");

/**
 * @var $slot \Concrete\Core\Board\Instance\Slot\Content\ContentSlotRenderer
 */
?>

<div class="row">
    <div class="col-md-4">
        <?php $slot->display(1); ?>
    </div>
    <div class="col-md-4">
        <?php $slot->display(2); ?>
    </div>
    <div class="col-md-4">
        <?php $slot->display(3); ?>
    </div>
</div>
