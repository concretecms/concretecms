<div class="ccm-pane-controls">

<?

$children = $c->getCollectionChildrenArray();

$numChildren = count($children);

?>

<script type="text/javascript">
	var childPages = new Array();
	<? foreach($children as $cID) { ?>
		childPages.push(<?=$cID?>);
	<? } ?>
</script>

<style type="text/css">
div#ccm-mc-page h1#ccm-sitemap-title {display: none}
</style>

<h1>Move, Copy or Delete this Page</h1>

<div class="ccm-form-area" id="ccm-mc-page">	
	<h2>Move/Copy Page</h2>
	<p>Using the buttons below, you may either Move/Copy this page and remain here, or jump completely to your dashboard sitemap, where you'll be able to drag and drop pages and change your entire site's hierarchy.</p>
		
<? /*
		<br/>
		<a href="javascript:void(0)" onclick="window.location.href='<?=DIR_REL?>/dashboard/sitemap/?reveal=<?=$c->getCollectionID()?>'" class="ccm-button-right cancel" style="margin-right: 10px"><span>Go to Dashboard</span></a>
		<a href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/sitemap_overlay.php?reveal=<?=$c->getCollectionID()?>&sitemap_mode=move_copy_delete" dialog-title="Choose Page" dialog-width="600" dialog-height="440" dialog-modal="false" class="ccm-button-right cancel" id="ccm-launch-sitemap"><span>Move/Copy Page</span></a>
*/ ?>

	<? 
	
	$args = array();
	$args['reveal'] = $c->getCollectionID();
	$args['sitemap_mode'] = 'move_copy_delete';
	Loader::element('dashboard/sitemap', $args);
	
	?>
	
	<script type="text/javascript">$(function() {
		$('#ccm-launch-sitemap').dialog();
	});
	</script>

	<div class="ccm-spacer">&nbsp;</div>
</div>

<div class="ccm-form-area" style="margin-top: 10px">
			<? if (!$cp->canDeleteCollection()) { ?>
				<h2>Delete Page</h2>
			
				You may not delete this page.
			<? } else if ($c->getCollectionID() == 1) {  ?>
				<h2>Delete Page</h2>
				You may not delete the home page.
			<? } else {	?>
				<? if ($c->isPendingDelete()) { ?>
					<h2>Delete Page</h2>
					<span class="important">This page has been marked for deletion.</span>
					<?
					
					$u = new User();
					$puID = $u->getUserID();
					
					if ($puID == $c->getPendingActionUserID()) { ?>
						<br><br>
						You marked this page for deletion on <strong><?=$c->getPendingActionDateTime()?></strong>.<br><br>
						<form method="get" id="ccmDeletePageForm" action="<?=DIR_REL?>/<?=DISPATCHER_FILENAME?>">
							<a href="javascript:void(0)" onclick="$('#ccmDeletePageForm').get(0).submit()" class="ccm-button-left"><span>Cancel</span></a>
							<input type="hidden" name="cID" value="<?=$c->getCollectionID()?>">
							<input type="hidden" name="ctask" value="clear_pending_action">
						</form>
					<? } ?>
				<? } else if ($c->isPendingMove() || $c->isPendingCopy()) { ?>
					<h2>Delete Page</h2>
					Since this page is being moved or copied, it cannot be deleted.
				<? } else if ($numChildren > 0 && !$cp->canAdminPage()) { ?>
					<h2>Delete Page</h2>
					Before you can delete this page, you must delete all of its child pages.
				<? } else { ?>
					
					<div class="ccm-buttons">

					<form method="get" id="ccmDeletePageForm" action="<?=$c->getCollectionAction()?>">		
						<a href="javascript:void(0)" onclick="if (confirm('Are you sure you wish to delete this page?')) { $('#ccmDeletePageForm').get(0).submit()}" class="ccm-button-right accept"><span>Delete Page</span></a>
					<h2>Delete Page</h2>
					Click "Delete" to delete this page. 
					<? if ($cp->canAdminPage() && $numChildren > 0) { ?>
						<br><br><span class="important">This will remove <?=$numChildren?> child page(s).</span>
					<? } ?>
						<input type="hidden" name="cID" value="<?=$c->getCollectionID()?>">
						<input type="hidden" name="ctask" value="delete">
					</form>
					</div>
					
				<? }
			}?>
<div class="ccm-spacer">&nbsp;</div>
</div>

</div>