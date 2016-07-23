<?php
defined('C5_EXECUTE') or die("Access Denied.");

/**
 * @var $listView \Concrete\Core\Notification\View\StandardListViewInterface
 */

?>

    <div class="ccm-block-desktop-waiting-for-me-description">
        <?php print $listView->getActionDescription() ?>
    </div>

    <?php
    $author = $listView->getInitiatorUserObject();
    if (is_object($author)) { ?>
        <div class="ccm-block-desktop-waiting-for-me-about">

            <?php print $listView->getInitiatorActionDescription() ?>

        </div>
    <?php } ?>