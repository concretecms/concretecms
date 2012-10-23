<?
defined('C5_EXECUTE') or die("Access Denied.");
$u = new User();
$form = Loader::helper('form');
$sh = Loader::helper('concrete/dashboard/sitemap');
if (!$sh->canRead()) {
	die(t('Access Denied'));
}
$u = new User();

if ($_POST['task'] == 'delete_pages') {

	$q = Queue::get('delete_page_request');

	if ($_POST['process']) {
		$obj = new stdClass;
		$js = Loader::helper('json');
		$messages = $q->receive(DELETE_PAGES_LIMIT);
		foreach($messages as $key => $p) {
			// delete the page here
			$page = unserialize($p->body);
			$c = Page::getByID($page['cID']);
			if ($c->getCollectionID() > 1) {
				$pkr = new DeletePagePageWorkflowRequest();
				$pkr->setRequestedPage($c);
				$pkr->setRequesterUserID($u->getUserID());
				$u->unloadCollectionEdit($c);
				$pkr->trigger();
			}
			$q->deleteMessage($p);
		}
		$obj->totalItems = $q->count();	
		if ($q->count() == 0) {
			$q->deleteQueue('delete_page_request');
		}
		print $js->encode($obj);
		exit;
	} else if ($q->count() == 0) {
		if (is_array($_POST['cID'])) {
			foreach($_POST['cID'] as $cID) {
				$c = Page::getByID($cID);
				$cp = new Permissions($c);
				$children = $c->getNumChildren();
				if (($u->isSuperUser() || $children == 0) && $cp->canDeletePage()) {
					$c->queueForDeletionRequest();
				}
			}
		}
	}

	$totalItems = $q->count();
	Loader::element('progress_bar', array('totalItems' => $totalItems, 'totalItemsSummary' => t2("%d page", "%d pages", $totalItems)));
	exit;
}

$form = Loader::helper('form');

$pages = array();
if (is_array($_REQUEST['cID'])) {
	foreach($_REQUEST['cID'] as $cID) {
		$pages[] = Page::getByID($cID);
	}
} else {
	$pages[] = Page::getByID($_REQUEST['cID']);
}

$pcnt = 0;
foreach($pages as $c) { 
	$cp = new Permissions($c);
	if ($cp->canDeletePage()) {
		$pcnt++;
	}
}

$searchInstance = Loader::helper('text')->entities($_REQUEST['searchInstance']);

?>
<div class="ccm-ui">

<? if ($pcnt == 0) { ?>
	<?=t("You do not have permission to delete any of the selected pages."); ?>
<? } else { ?>

	<?=t('Are you sure you want to delete the following pages?')?><br/><br/>

	<form id="ccm-<?=$searchInstance?>-delete-form" method="post" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/pages/delete">
	<?=$form->hidden('task', 'delete_pages')?>
	<table border="0" cellspacing="0" cellpadding="0" width="100%" class="table table-striped">
	<tr>
		<th><?=t('Name')?></th>
		<th><?=t('Page Type')?></th>
		<th><?=t('Date Added')?></th>
		<th><?=t('Author')?></th>
	</tr>
	
	<? foreach($pages as $c) { 
		$cp = new Permissions($c);
		$c->loadVersionObject();
		if ($cp->canDeletePage() && $c->getCollectionID() > 1) { ?>
		
		<?=$form->hidden('cID[]', $c->getCollectionID())?>		
		
		<tr>
			<td class="ccm-page-list-name"><?=$c->getCollectionName()?></td>
			<td><?=$c->getCollectionTypeName()?></td>
			<td><?=date(DATE_APP_DASHBOARD_SEARCH_RESULTS_PAGES, strtotime($c->getCollectionDatePublic()))?></td>
			<td><?
				$ui = UserInfo::getByID($c->getCollectionUserID());
				if (is_object($ui)) {
					print $ui->getUserName();
				}
			}?></td>
		
		</tr>
		
		<? }  ?>
	</table>
	</form>
	<div class="dialog-buttons">
	<? $ih = Loader::helper('concrete/interface')?>
	<?=$ih->button_js(t('Cancel'), 'jQuery.fn.dialog.closeTop()', 'left', 'btn')?>	
	<?=$ih->button_js(t('Delete'), 'ccm_sitemapDeletePages(\'' . $searchInstance . '\')', 'right', 'btn error')?>
	</div>		
		
	<?
	
}
?>
</div>