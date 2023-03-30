<?php defined('C5_EXECUTE') or die("Access Denied.");

/**
 * @var $slot \Concrete\Core\Board\Instance\Slot\SlotRenderer
 */

?>

<div class="container ccm-board-blog">
    <div class="row pb-4 mb-4">
        <div class="col-md-12 blog-featured-post">
            <?php
            $slot->display(1);
            ?>
            <hr class="d-md-none d-block mb-0">
        </div>
    </div>
    <div class="row gx-8">
        <div class="col-md-8">
            <?php
            if ($slot->hasContents(2)) {
            ?>
            <div class="row">
                <div class="col-md-12">
                    <?php
                    $slot->display(2);
                    ?>
                </div>
            </div>
            <?php } ?>
            <?php
            if ($slot->hasContents(3)) {
            ?>
            <div class="row">
                <div class="col-md-12">
                    <hr class="d-none d-md-block">
                    <?php
                    $slot->display(3);
                    ?>
                </div>
            </div>
            <?php } ?>
            <?php
            if ($slot->hasContents(4)) {
            ?>
            <div class="row">
                <div class="col-md-12">
                    <hr class="d-none d-md-block">
                    <?php
                    $slot->display(4);
                    ?>
                </div>
            </div>
            <?php } ?>
            <?php
            if ($slot->hasContents(5)) {
            ?>
            <div class="row">
                <div class="col-md-12">
                    <hr class="d-none d-md-block">
                    <?php
                    $slot->display(5);
                    ?>
                </div>
            </div>
            <?php } ?>
        </div>
        <div class="col-md-4 col-blog-sidebar mt-3 mt-md-0">
            <?php
            $stack = Stack::getByName('Blog Sidebar');
            if ($stack) {
                $stack->display();
            }
            ?>
        </div>
    </div>
</div>
