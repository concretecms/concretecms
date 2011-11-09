<?

defined('C5_EXECUTE') or die("Access Denied.");
$dh = Loader::helper('concrete/dashboard/sitemap');
if (!$dh->canRead()) {
	die(t("Access Denied."));
}

$c = Page::getByID($_REQUEST['cID']);
if (is_object($c) && !$c->isError()) { 
	$cp = new Permissions($c);
	if ($c->getPendingActionTargetCollectionID()) {
		$target = Page::getByID($c->getPendingActionTargetCollectionID());
		if (!is_object($target) || $target->isError()) { 
			$target = Page::getByID(HOME_CID);		
		}
	}
	
	$doAdd = false;
	if ($c->getCollectionTypeID() > 0) { 
		$ct = CollectionType::getByID($c->getCollectionTypeID());
		$ncp = new Permissions($target);
		if ($ncp->canAddSubCollection($ct)) {
			$doAdd = true;
		}
	} else if ($u->isSuperUser()) {
		$doAdd = true;
	}

	$obj = new stdClass;
	if ($doAdd) { 	
		$c->clearPendingAction();
		$c->activate();
		$c->move($target);
	
		$obj->message = t("Page Restored");
		$obj->targetCID = $target->getCollectionID();
	} else {
		$obj->message = t('Unable to restore page.');
	}
	print Loader::helper('json')->encode($obj);
}