<?
defined('C5_EXECUTE') or die("Access Denied.");
?>

<? if ($c->getCollectionID() == 1) {  ?>
	<div class="error alert-message"><?=t('You may not delete the home page.');?></div>
	<div class="dialog-buttons"><input type="button" class="btn" value="<?=t('Cancel')?>" onclick="jQuery.fn.dialog.closeTop()" /></div>
	
<? }  else if ($numChildren > 0 && !$u->isSuperUser()) { ?>
	<div class="error alert-message"><?=t('Before you can delete this page, you must delete all of its child pages.')?></div>
	<div class="dialog-buttons"><input type="button" class="btn" value="<?=t('Cancel')?>" onclick="jQuery.fn.dialog.closeTop()" /></div>
<? } else { ?>		
	<form method="post" data-dialog-form="delete-page" action="<?=$controller->action('submit')?>">
		<input type="hidden" name="rel" value="<?php echo h($request_rel); ?>" />

		<div class="dialog-buttons">
		<button class="btn pull-left" data-dialog-action="cancel"><?=t('Cancel')?></button>
		<button type="button" data-dialog-action="submit" class="btn btn-danger pull-right"><?=t('Delete')?></button>
		</div>

		<? if($c->isSystemPage()) { ?>
			<div class="alert alert-error"><?php echo t('Warning! This is a system page. Deleting it could potentially break your site. Please proceed with caution.') ?></div>
		<? } ?>
		<h3><?=t('Are you sure you wish to delete this page?')?></h3>
		<? if ($u->isSuperUser() && $numChildren > 0) { ?>
			<h4><?=t2('This will remove %s child page.', 'This will remove %s child pages.', $numChildren, $numChildren)?></h4>
		<? } ?>
		
		<? if (ENABLE_TRASH_CAN) { ?>
			<p><?=t('Deleted pages are moved to the trash can in the sitemap.')?></p>
		<? } else { ?>
			<p><?=t('This cannot be undone.')?></p>
		<? } ?>
		
	</form>

	<script type="text/javascript">
	$(function() {
		ccm_event.subscribe('AjaxFormSubmitSuccess', function(e) {
			if (e.eventData.form == 'delete-page') {
				ccm_event.publish('SitemapDeleteRequestComplete', {'cID': '<?=$c->getCollectionID()?>'});
			}
		});
	});
	</script>
		
	<? }
?>