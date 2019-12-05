<?php defined('C5_EXECUTE') or die("Access Denied.");

/**
 * @var $slot \Concrete\Core\Board\Instance\Slot\SlotRenderer
 */
?>

<div class="container p-5 bg-light">
    <div class="row mb-4">
        <div class="col-4">
            <?php
            $slot->display(1);
            ?>
        </div>
        <div class="col-4">
            <?php
            $slot->display(2);
            ?>
        </div>
        <div class="col-4">
            <?php
            $slot->display(3);
            ?>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-4">
            <?php
            $slot->display(4);
            ?>
        </div>
        <div class="col-4">
            <?php
            $slot->display(5);
            ?>
        </div>
        <div class="col-4">
            <?php
            $slot->display(6);
            ?>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-4">
            <?php
            $slot->display(7);
            ?>
        </div>
        <div class="col-4">
            <?php
            $slot->display(8);
            ?>
        </div>
        <div class="col-4">
            <?php
            $slot->display(9);
            ?>
        </div>
    </div>
</div>
