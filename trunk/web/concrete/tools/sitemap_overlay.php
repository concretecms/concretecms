<?
defined('C5_EXECUTE') or die(_("Access Denied."));


$args = $_REQUEST;
$args['reveal'] = $_REQUEST['cID'];
$args['sitemapCombinedMode'] = $sitemapCombinedMode;
Loader::element('dashboard/sitemap', $args);
?>
