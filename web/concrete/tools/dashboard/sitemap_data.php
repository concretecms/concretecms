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

if (!is_array($_SESSION['dsbSitemapNodes'])) {
	$_SESSION['dsbSitemapNodes'] = array();
	if (isset($_REQUEST['node'])) {
		$_SESSION['dsbSitemapNodes'][] = $_REQUEST['node'];
	} else {
		$_SESSION['dsbSitemapNodes'][] = 1;
	}
} else if ($_REQUEST['ctask'] == 'close-node') {
	for ($i = 0; $i < count($_SESSION['dsbSitemapNodes']); $i++) {
		if ($_SESSION['dsbSitemapNodes'][$i] == $_REQUEST['node']) {
			unset($_SESSION['dsbSitemapNodes'][$i]);
		}
	}
	
	// rescan the nodes
	$tempArray = array();
	foreach($_SESSION['dsbSitemapNodes'] as $dsb) {
		$tempArray[] = $dsb;
	}
	$_SESSION['dsbSitemapNodes'] = $tempArray;
	
	$js = Loader::helper('json');
	print $js->encode(array());
	
	unset($tempArray);
	exit;
} else {
	if (!in_array($_REQUEST['node'], $_SESSION['dsbSitemapNodes'])) {
		$_SESSION['dsbSitemapNodes'][] = $_REQUEST['node'];
	}
}

$node = (isset($_REQUEST['node'])) ? $_REQUEST['node'] : 0;

$nodes = $dh->getSubNodes($node);
print Loader::helper('json')->encode($nodes);