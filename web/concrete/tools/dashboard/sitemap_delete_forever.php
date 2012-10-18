<?
defined('C5_EXECUTE') or die("Access Denied.");
$dh = Loader::helper('concrete/dashboard/sitemap');
if (!$dh->canRead()) {
	die(t("Access Denied."));
}

$q = Queue::get('delete_page');
$isEmptyTrash = false;

if ($_POST['process']) {
	$obj = new stdClass;
	$obj->totalItems = $q->count();	
	$js = Loader::helper('json');
	$messages = $q->receive(DELETE_PAGES_LIMIT);
	foreach($messages as $key => $p) {
		// delete the page here
		$page = unserialize($p->body);
		$c = Page::getByID($page['cID']);
		$c->delete();
		$q->deleteMessage($p);
	}
	print $js->encode($obj);
	exit;
} else {
	$c = Page::getByID($_REQUEST['cID']);
	if ($c->getCollectionPath() == TRASH_PAGE_PATH) {
		$isEmptyTrash = true;
	}
	if (is_object($c) && !$c->isError()) { 
		$cp = new Permissions($c);
		if ($cp->canDeletePage()) { 
			$c->queueForDeletion();
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