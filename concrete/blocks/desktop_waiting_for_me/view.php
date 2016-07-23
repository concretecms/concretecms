<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="ccm-block-desktop-waiting-for-me">
    <div data-list="notification">

    <h3><?=t('Waiting For Me')?></h3>

    <?php

        foreach($items as $item) {
            $notification = $item->getNotification();
            $view = $notification->getListView();

            ?>

        <div class="ccm-block-desktop-waiting-for-me-item" data-notification-alert-id="<?=$item->getNotificationAlertID()?>"
        data-token="<?=$token->generate()?>">
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

        <p <?php if (count($items)) { ?> style="display: none"<?php }  ?> data-notification-description="empty"><?=t('There are no items that currently need your attention.')?></p>

    </div>


    <script type="text/javascript">
        $(function() {
            $('div[data-list=notification]').concreteNotificationList();
        });
    </script>

</div>
