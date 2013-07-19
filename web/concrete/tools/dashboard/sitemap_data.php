<?

defined('C5_EXECUTE') or die("Access Denied.");
$dh = Loader::helper('concrete/dashboard/sitemap');
if (!$dh->canRead()) {
	die(t("Access Denied."));
}

if (isset($_REQUEST['selectedPageID'])) {
	if (strstr($_REQUEST['selectedPageID'], ',')) {
		$sanitizedPageID = preg_replace('/[^0-9,]/', '', $_REQUEST['selectedPageID']);
		$sanitizedPageID = preg_replace('/\s/', '', $sanitizedPageID);
	} else {
		$sanitizedPageID = intval($_REQUEST['selectedPageID']);
	}
	$dh->setSelectedPageID($sanitizedPageID);
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

if (!$_REQUEST['keywords']) { // if there ARE keywords then we don't want to cache the node 
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
}

$node = (isset($_REQUEST['node'])) ? $_REQUEST['node'] : 0;

if ($_REQUEST['mode'] == 'move_copy_delete') {
	$nodes = $dh->getSubNodes($node, $_REQUEST['level'], $_REQUEST['keywords'], false);
} else {
	$nodes = $dh->getSubNodes($node, $_REQUEST['level'], $_REQUEST['keywords']);
}

$js = Loader::helper('json');
$th= Loader::helper('text');
$instance_id = addslashes($th->entities($_REQUEST['instance_id']));
$display_mode = addslashes($th->entities($_REQUEST['display_mode']));
$select_mode = addslashes($th->entities($_REQUEST['select_mode']));
print $dh->outputRequestHTML($instance_id, $display_mode, $select_mode, $nodes);
$dh->clearOneTimeActiveNodes();