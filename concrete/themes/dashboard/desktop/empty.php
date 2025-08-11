<?php
defined('C5_EXECUTE') or die("Access Denied.");
$view->inc('elements/header.php', array('bodyClass' => 'ccm-dashboard-desktop'));

print $innerContent;

?>

<?php $view->inc('elements/footer.php'); ?>
