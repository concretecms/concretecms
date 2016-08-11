<?php defined('C5_EXECUTE') or die('Access Denied'); ?>
<?php
$dh = Loader::helper('concrete/dashboard/sitemap');
if ($dh->canRead()) {
    ?>

	<div class="ccm-dashboard-content-full">
		<?php Loader::element('pages/search', array('result' => $result))?>
	</div>

<?php
} else {
    ?>
	<p><?=t("You must have access to the dashboard sitemap to search pages.")?></p>
<?php
} ?>