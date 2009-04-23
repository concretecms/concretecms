<?php 

defined('C5_EXECUTE') or die(_("Access Denied."));
$ch = Page::getByPath("/dashboard/sitemap");
$chp = new Permissions($ch);
if (!$chp->canRead()) {
	die(_("Access Denied."));
}

$dh = Loader::helper('concrete/dashboard/sitemap');

if ($_REQUEST['search']) {

	Loader::model('search/collection');
	$searchArray = $_GET;
	$s = new CollectionSearch($searchArray);

	if($_GET['sort'] == 'cvDateCreated') { $_GET['order'] = "desc"; }
	if($_GET['sort'] == 'postDate') { $_GET['order'] = "desc"; }

	// default return value is to low, we will be returning 1000's ...
	// only if we are sorting by postDate

	$res = $s->getResult($_GET['sort'], $_GET['start'], $_GET['order'], $_GET['view'], 50);
	if ($s->getTotal() > 0) {
		$nodes = array();
		while ($row = $res->fetchRow()) {

			$breadcrumb = array();

			if($row['cParentID']!=0 && $row['cParentID']!=1 && $row['cParentID'] != $lastCParentID) {
				
				$s->resetCollectionIDArray();
				$s->populateCollectionIDArray($row[cID]);
				$cIDArrayREV = array_reverse($s->cIDArray);
				foreach($cIDArrayREV as $cIDItem) {
					if($cIDItem > 1) {
						$ncd = Page::getByID($cIDItem);
						$breadcrumb[] = array('cID' => $cIDItem, 'cvName' => $ncd->getCollectionName());
					}
				}
			}
			
			$row['id'] = $row['cID'];
			$row['cDateAdded'] = date('F d, Y', strtotime($row['cDateAdded']));
			$row['breadcrumb'] = $breadcrumb;
			$nodes[] = $row;
		}
	} else {
		$nodes = array();
	}
	
} else { 
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

}

$js = Loader::helper('json');
print $js->encode($nodes);
$dh->clearOneTimeActiveNodes();
?>