<?php
defined('C5_EXECUTE') or die("Access Denied.");

/**
 * @var $listView \Concrete\Core\Notification\View\StandardListViewInterface
 */

if ($listView->getActions()) {

?>


<?php } else { ?>

    <button type="button" data-notification-action="archive" class="btn btn-default btn-waiting-for-me-archive">
        <i></i>
    </button>

<?php } ?>