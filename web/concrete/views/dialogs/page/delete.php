<?
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div class="ccm-ui">
<? if ($c->getCollectionID() == 1) {  ?>
	<div class="alert alert-error"><?=t('You may not delete the home page.');?></div>
	<div class="dialog-buttons"><input type="button" class="btn btn-default" value="<?=t('Cancel')?>" onclick="jQuery.fn.dialog.closeTop()" /></div>

<? }  else if ($numChildren > 0 && !$u->isSuperUser()) { ?>
	<div class="alert alert-error"><?=t('Before you can delete this page, you must delete all of its child pages.')?></div>
	<div class="dialog-buttons"><input type="button" class="btn btn-default" value="<?=t('Cancel')?>" onclick="jQuery.fn.dialog.closeTop()" /></div>
<? } else { ?>
	<form method="post" data-dialog-form="delete-page" action="<?=$controller->action('submit')?>">
        <? if ($sitemap) { ?>
            <input type="hidden" name="sitemap" value="1" />
        <? } ?>

		<div class="dialog-buttons">
		<button class="btn btn-default pull-left" data-dialog-action="cancel"><?=t('Cancel')?></button>
		<button type="button" data-dialog-action="submit" class="btn btn-danger pull-right"><?=t('Delete')?></button>
		</div>

		<? if($c->isSystemPage() && !$c->isPageDraft()) { ?>
			<div class="alert alert-danger"><?php echo t('Warning! This is a system page. Deleting it could potentially break your site. Please proceed with caution.') ?></div>
		<? } ?>
		<p><?=t('Are you sure you wish to delete this page?')?></p>
		<? if ($u->isSuperUser() && $numChildren > 0) { ?>
			<strong><?=t2('This will remove %s child page.', 'This will remove %s child pages.', $numChildren, $numChildren)?></strong>
		<? } ?>

		<? if (Config::get('concrete.misc.enable_trash_can')) { ?>
			<p><?=t('Deleted pages are moved to the trash can in the sitemap.')?></p>
		<? } else { ?>
			<p><?=t('This cannot be undone.')?></p>
		<? } ?>

	</form>

	<script type="text/javascript">
	$(function() {
        ConcreteEvent.unsubscribe('AjaxFormSubmitSuccess.sitemapDelete');
		ConcreteEvent.subscribe('AjaxFormSubmitSuccess.sitemapDelete', function(e, data) {
			if (data.form == 'delete-page') {
				ConcreteEvent.publish('SitemapDeleteRequestComplete', {'cID': '<?=$c->getCollectionID()?>'});
			}
		});
	});
	</script>

	<? }
?>

</div>
