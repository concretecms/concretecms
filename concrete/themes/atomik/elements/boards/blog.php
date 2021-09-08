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
        </div>
    </div>
    <div class="row gx-8">
        <div class="col-md-8">
            <div class="row gx-8">
                <div class="col-md-6">
                    <?php
                    $slot->display(2);
                    ?>
                </div>
                <div class="col-md-6">
                    <?php
                    $slot->display(3);
                    ?>
                </div>
                <div class="col-md-12">
                    <hr>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <?php
                    $slot->display(4);
                    ?>
                    <hr>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <?php
                    $slot->display(5);
                    ?>
                    <hr>
                </div>
            </div>
            <div class="row gx-8">
                <div class="col-md-6">
                    <?php
                    $slot->display(6);
                    ?>
                </div>
                <div class="col-md-6">
                    <?php
                    $slot->display(7);
                    ?>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-blog-sidebar">
            <?php
            $stack = Stack::getByName('Blog Sidebar');
            if ($stack) {
                $stack->display();
            }
            ?>
        </div>
    </div>
</div>
