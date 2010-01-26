<?
defined('C5_EXECUTE') or die(_("Access Denied."));


$args = $_REQUEST;
$args['reveal'] = $_REQUEST['cID'];
Loader::element('dashboard/sitemap', $args);
?>
