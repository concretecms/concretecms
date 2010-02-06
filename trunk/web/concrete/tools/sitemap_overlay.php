<?
defined('C5_EXECUTE') or die(_("Access Denied."));

$sh = Loader::helper('concrete/dashboard/sitemap');
if (!$sh->canRead()) {
	die(t('Access Denied'));
}

$args = $_REQUEST;
$args['selectedPageID'] = $_REQUEST['cID'];
$args['sitemapCombinedMode'] = $sitemapCombinedMode;
if (!isset($args['select_mode'])) {
	$args['select_mode'] = 'select_mode';
}

$args['display_mode'] = 'full';
$args['instance_id'] = time();
$args['node'] = 0;
Loader::element('dashboard/sitemap', $args);
?>
