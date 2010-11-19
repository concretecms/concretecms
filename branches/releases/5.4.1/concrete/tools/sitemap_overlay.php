<?php 
defined('C5_EXECUTE') or die("Access Denied.");

$sh = Loader::helper('concrete/dashboard/sitemap');
if (!$sh->canRead()) {
	die(t('Access Denied'));
}

$args = $_REQUEST;
if (isset($select_mode)) {
	$args['select_mode'] = $select_mode;
}
$args['selectedPageID'] = $_REQUEST['cID'];
$args['sitemapCombinedMode'] = $sitemapCombinedMode;
if (!isset($args['select_mode'])) {
	$args['select_mode'] = 'select_page';
}
if ($args['select_mode'] == 'select_page') {
	$args['reveal'] = $args['selectedPageID'];
}

$args['display_mode'] = 'full';
$args['instance_id'] = time();
Loader::element('dashboard/sitemap', $args);
?>
