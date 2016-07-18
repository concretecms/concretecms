<?php
defined('C5_EXECUTE') or die("Access Denied.");

/**
 * @var $listView \Concrete\Core\Notification\View\ListViewInterface
 */

?>

<div class="" data-notification-id="<?=$listView->getNotificationObject()->getNotificationID()?>">
    <form action="" method="post">

        <div class="">
            <?=$listView->renderIcon()?>
        </div>

        <div class="">
            <?=$listView->renderDetails()?>
        </div>

        <div class="">
            <?=$listView->renderMenu()?>
        </div>

    </form>
</div>


