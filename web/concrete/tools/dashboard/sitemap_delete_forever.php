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
	$obj->totalPages = $q->count();	
	$js = Loader::helper('json');
	$messages = $q->receive(DELETE_PAGES_LIMIT);
	foreach($messages as $key => $p) {
		// delete the page here
		$q->deleteMessage($p);
		$page = unserialize($p->body);
		$c = Page::getByID($page['cID']);
		$cp = new Permissions($c);
		if ($cp->canDeletePage()) { 
			$c->delete();
		}
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
			$totalPages = $q->count();
		}
	}
}
?>

<div class="ccm-ui">
	<div id="delete-progress-bar">
	<div class="progress progress-striped active">
	<div class="bar" style="width: 0%;"></div>
	</div>
	</div>
</div>

<script type="text/javascript">
$(function() {
	ccm_deleteForeverLoop = function() {
		$.ajax({
			url: '<?=REL_DIR_FILES_TOOLS_REQUIRED?>/dashboard/sitemap_delete_forever',
			dataType: 'json',
			type: 'POST',
			data: {
				'process': true,
				'cID': '<?=$_REQUEST['cID']?>'
			},
			success: function(r) {
				var totalPagesLeft = r.totalPages;
				// update the percentage
				var pct = Math.round(((<?=$totalPages?> - totalPagesLeft) / <?=$totalPages?>) * 100);
				$('#delete-progress-bar div.bar').width(pct + '%');
				if (totalPagesLeft > 0) {
					setTimeout(function() {
						ccm_deleteForeverLoop();
					}, 250);
				} else {
					setTimeout(function() {
						// give the animation time to catch up.
						<? if ($isEmptyTrash) { ?>
							closeSub("<?=$_REQUEST['instance_id']?>", "<?=$_REQUEST['cID']?>", 'full', '');
							var container = $("ul[tree-root-node-id=<?=$_REQUEST['cID']?>]").parent();
							container.find('img.tree-plus').remove();
							container.find('span.ccm-sitemap-num-subpages').remove();
						<? } else { ?>
							deleteBranchFade("<?=$_REQUEST['cID']?>");
							ccmAlert.hud("<?=t('Page(s) deleted.')?>", 2000);
						<? } ?>
							$('#ccm-sitemap-delete-forever').dialog('close');
					}, 1000);
				}
			}
		});
	}
	ccm_deleteForeverLoop();		
});
</script>