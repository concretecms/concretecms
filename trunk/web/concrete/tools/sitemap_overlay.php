<?
defined('C5_EXECUTE') or die(_("Access Denied."));

$sh = Loader::helper('concrete/dashboard/sitemap');
if (!$sh->canRead()) {
	die(t('Access Denied'));
}

$args = $_REQUEST;
$args['reveal'] = $_REQUEST['cID'];
$args['sitemapCombinedMode'] = $sitemapCombinedMode;
if (!isset($args['sitemap_mode'])) {
	$args['sitemap_mode'] = 'move_copy_delete';
}
Loader::element('dashboard/sitemap', $args);
?>
