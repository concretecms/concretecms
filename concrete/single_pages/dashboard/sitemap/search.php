<?php defined('C5_EXECUTE') or die('Access Denied'); ?>
<?php
$dh = Loader::helper('concrete/dashboard/sitemap');
if ($dh->canRead()) {
    ?>

    <?php Loader::element('pages/search', array('result' => $result))?>

<?php
} else {
    ?>
	<p><?=t("You must have access to the dashboard sitemap to search pages.")?></p>
<?php
} ?>
