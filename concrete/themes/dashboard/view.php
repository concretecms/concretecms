<?php
defined('C5_EXECUTE') or die('Access Denied.');
$view->inc('elements/header.php');
$view->inc('elements/title.php');
?>

<div id="ccm-dashboard-content-regular">
    <?php
    $view->inc('elements/result_messages.php');
    print $innerContent;
    ?>
</div>

<?php
$view->inc('elements/footer.php');
