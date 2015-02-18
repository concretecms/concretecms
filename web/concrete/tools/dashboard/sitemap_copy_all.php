<?php
defined('C5_EXECUTE') or die("Access Denied.");
$dh = Loader::helper('concrete/dashboard/sitemap');
if (!$dh->canRead()) {
	die(t("Access Denied."));
}

// we have to do this otherwise permissions pointers aren't correct
// (cInheritPermissionsFromCID on parent nodes)
Core::make('cache/request')->disable();

$q = Queue::get('copy_page');
$includeParent = true;
if ($_REQUEST['copyChildrenOnly']) {
	$includeParent = false;
}
$db = Loader::db();

if ($_POST['process']) {
	$obj = new stdClass;
	$js = Loader::helper('json');
	$messages = $q->receive(Config::get('concrete.limits.copy_pages'));
	foreach($messages as $key => $p) {
		// delete the page here
		$page = unserialize($p->body);
		$oc = Page::getByID($page['cID']);
		// this is the page we're going to copy.
		// now we check to see if the parent ID of the current record has already been duplicated somewhere.
		$newCID = $db->GetOne('select cID from QueuePageDuplicationRelations where originalCID = ? and queue_name = ?', array($page['cParentID'], 'copy_page'));
		if ($newCID > 0) {
			$dc = Page::getByID($newCID);
		} else {
			$dc = Page::getByID($page['destination']);
		}
		$nc = $oc->duplicate($dc);
		$ocID = $oc->getCollectionID();
		$ncID = $nc->getCollectionID();
		if ($oc->getCollectionPointerOriginalID() > 0) {
			$ocID = $oc->getCollectionPointerOriginalID();
		}
		if ($nc->getCollectionPointerOriginalID() > 0) {
			$ncID = $nc->getCollectionPointerOriginalID();
		}
		$db->Execute('insert into QueuePageDuplicationRelations (cID, originalCID, queue_name) values (?, ?, ?)', array(
			$ncID, $ocID, 'copy_page'
		));

		$q->deleteMessage($p);
	}
	$obj->totalItems = $q->count();
	print $js->encode($obj);
	if ($q->count() == 0) {
		$q->deleteQueue('copy_page');
		$db->Execute('truncate table QueuePageDuplicationRelations');
	}
	exit;

} else if ($q->count() == 0) {
	if (isset($_REQUEST['origCID'] ) && strpos($_REQUEST['origCID'], ',') > -1) {
		$ocs = explode(',', $_REQUEST['origCID']);
		foreach($ocs as $ocID) {
			$oc = Page::getByID($ocID);
			if (is_object($oc) && !$oc->isError()) {
				$originalPages[] = $oc;
			}
		}
	} else {
		$oc = Page::getByID($_REQUEST['origCID']);
		if (is_object($oc) && !$oc->isError()) {
			$originalPages[] = $oc;
		}
	}

	$dc = Page::getByID($_REQUEST['destCID']);
	if (count($originalPages) > 0 && is_object($dc) && !$dc->isError()) {
		$u = new User();
		if ($u->isSuperUser() && $oc->canMoveCopyTo($dc)) {
			foreach($originalPages as $oc) {
				$oc->queueForDuplication($dc, $includeParent);
				$totalItems = $q->count();
			}
		}
	}
}
$totalItems = $q->count();
Loader::element('progress_bar', array('totalItems' => $totalItems, 'totalItemsSummary' => t2("%d page", "%d pages", $totalItems)));
