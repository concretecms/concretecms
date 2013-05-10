<?

defined('C5_EXECUTE') or die("Access Denied.");
$dh = Loader::helper('concrete/dashboard/sitemap');
if (!$dh->canRead()) {
	die(t("Access Denied."));
}

if (isset($_REQUEST['selectedPageID'])) {
	$dh->setSelectedPageID(intval($_REQUEST['selectedPageID']));
}

if (isset($_REQUEST['task']) && $_REQUEST['task'] == 'save_sitemap_display_mode') {
	$u = new User();
	$u->saveConfig('SITEMAP_OVERLAY_DISPLAY_MODE', $_REQUEST['display_mode']);
	exit;
}


if (isset($_REQUEST['show_system'])) {
	$_SESSION['dsbSitemapShowSystem'] = $_REQUEST['show_system'];
	$js = Loader::helper('json');
	print $js->encode(array());
	exit;
}

$node = (isset($_REQUEST['node'])) ? $_REQUEST['node'] : 0;

$nodes = $dh->getSubNodes($node);
print Loader::helper('json')->encode($nodes);