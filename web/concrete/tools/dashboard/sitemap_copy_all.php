<?
defined('C5_EXECUTE') or die("Access Denied.");
$dh = Loader::helper('concrete/dashboard/sitemap');
if (!$dh->canRead()) {
	die(t("Access Denied."));
}

$q = Queue::get('copy_page');
$includeParent = true;
if ($_REQUEST['copyChildrenOnly']) {
	$includeParent = false;
}
$db = Loader::db();

if ($_POST['process']) {
	$obj = new stdClass;
	$obj->totalItems = $q->count();	
	$js = Loader::helper('json');
	$messages = $q->receive(COPY_PAGES_LIMIT);
	foreach($messages as $key => $p) {
		// delete the page here
		$page = unserialize($p->body);
		$oc = Page::getByID($page['cID']);
		// this is the page we're going to copy.
		// now we check to see if the parent ID of the current record has already been duplicated somewhere.
		$newCID = $db->GetOne('select cID from PageRelations where originalCID = ? and relationType = ?', array($page['cParentID'], 'C'));
		if ($newCID > 0) {
			$dc = Page::getByID($newCID);
		} else {
			$dc = Page::getByID($page['destination']);
		}
		$oc->duplicate($dc);
		$q->deleteMessage($p);
	}
	print $js->encode($obj);
	if ($q->count() == 0) {
		$db->Execute('truncate table PageRelations');
	}
	exit;

} else {
	$oc = Page::getByID($_REQUEST['origCID']);
	$dc = Page::getByID($_REQUEST['destCID']);
	if (is_object($oc) && !$oc->isError() && is_object($dc) && !$dc->isError()) { 
		$u = new User();
		if ($u->isSuperUser() && $oc->canMoveCopyTo($dc)) {
			$oc->queueForDuplication($dc, $includeParent);
			$totalItems = $q->count();
		}
	}
}
?>

<div class="ccm-ui">
	<div id="ccm-progressive-operation-progress-bar" data-total-items="<?=$totalItems?>">
	<div class="progress progress-striped active">
	<div class="bar" style="width: 0%;"></div>
	</div>
	</div>
</div>