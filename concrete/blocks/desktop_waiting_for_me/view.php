<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="ccm-block-desktop-waiting-for-me">

    <h3><?=t('Waiting For Me')?></h3>

    <?php if (count($items)) {

        foreach($items as $item) {
            $notification = $item->getNotification();
            $view = $notification->getListView();

            ?>

        <div class="ccm-block-desktop-waiting-for-me-item" data-notification-alert-id="<?=$item->getNotificationAlertID()?>">
            <form action="" method="post">

                <div class="ccm-block-desktop-waiting-for-me-icon">
                    <?php print $view->renderIcon() ?>
                </div>

                <div class="ccm-block-desktop-waiting-for-me-details">
                    <?php print $view->renderDetails() ?>
                </div>

                <div class="ccm-block-desktop-waiting-for-me-menu">
                    <?php print $view->renderMenu() ?>
                </div>


            </form>
        </div>

            <?php
        }

        ?>


    <?php } else { ?>

        <p><?=t('There are no items that currently need your attention.')?></p>

    <?php } ?>

</div>
