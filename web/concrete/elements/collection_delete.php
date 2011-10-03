<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="ccm-ui">
<?
$sh = Loader::helper('concrete/dashboard/sitemap');
$numChildren = $c->getNumChildren();
?>


<? if ($c->getCollectionID() == 1) {  ?>
	<div class="error alert-message"><?=t('You may not delete the home page.');?></div>
	<div class="dialog-buttons"><input type="button" class="btn" value="<?=t('Close')?>" onclick="jQuery.fn.dialog.closeTop()" /></div>
<? } else {	?>
	<? if ($c->isPendingDelete()) { ?>
		<div class="notice alert-message"><?=t('This page has been marked for deletion.');?></div>
		<?
		
		$u = new User();
		$puID = $u->getUserID();
		
		if ($puID == $c->getPendingActionUserID()) { ?>
			<?=t('You marked this page for deletion on <strong>%s</strong>', date(DATE_APP_PAGE_VERSIONS, strtotime($c->getPendingActionDateTime())))?><br><br>
	
			<form method="get" id="ccmDeletePageForm" action="<?=DIR_REL?>/<?=DISPATCHER_FILENAME?>">
				<div class="dialog-buttons"><input type="button" class="btn ccm-button-left" value="<?=t('Close')?>" onclick="jQuery.fn.dialog.closeTop()" />
					<input type="submit" class="btn ccm-button-right primary" onclick="$('#ccmDeletePageForm').submit()" value="<?=t('Cancel Delete')?>" />
				</div>
				<input type="hidden" name="cID" value="<?=$c->getCollectionID()?>">
				<input type="hidden" name="ctask" value="clear_pending_action">
			</form>
		<? } ?>
	<? } else if ($c->isPendingMove() || $c->isPendingCopy()) { ?>
		<div class="error alert-message"><?=t('Since this page is being moved or copied, it cannot be deleted.')?></div>
		<div class="dialog-buttons"><input type="button" class="btn" value="<?=t('Close')?>" onclick="jQuery.fn.dialog.closeTop()" /></div>
		
	<? } else if ($numChildren > 0 && !$cp->canAdminPage()) { ?>
		<div class="error alert-message"><?=t('Before you can delete this page, you must delete all of its child pages.')?></div>
		<div class="dialog-buttons"><input type="button" class="btn" value="<?=t('Close')?>" onclick="jQuery.fn.dialog.closeTop()" /></div>
		
	<? } else { 
		?>
		
		<div class="ccm-buttons">

		<form method="post" id="ccmDeletePageForm" action="<?=$c->getCollectionAction()?>">	
			<div class="dialog-buttons"><input type="button" class="btn" value="<?=t('Close')?>" onclick="jQuery.fn.dialog.closeTop()" />
			<a href="javascript:void(0)" onclick="$('#ccmDeletePageForm').get(0).submit()" class="ccm-button-right btn error"><span><?=t('Delete Page')?></span></a>
			</div>
		<h3><?=t('Are you sure you wish to delete this page?')?></h3>
		<? if ($cp->canAdminPage() && $numChildren > 0) { ?>
			<h4><?=t('This will remove %s child page(s).', $numChildren)?></h4>
		<? } ?>
		
		<p><?=t('This cannot be undone.')?></p>
			<input type="hidden" name="cID" value="<?=$c->getCollectionID()?>">
			<input type="hidden" name="ctask" value="delete">
		</form>
		</div>
		
	<? }
	
}?>