<?
defined('C5_EXECUTE') or die(_("Access Denied."));


$args = $_REQUEST;
$args['reveal'] = $_REQUEST['cID'];
$args['sitemapCombinedMode'] = $sitemapCombinedMode;
if (!isset($args['sitemap_mode'])) {
	$args['sitemap_mode'] = 'move_copy_delete';
}
Loader::element('dashboard/sitemap', $args);
?>
