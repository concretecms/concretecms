<?
defined('C5_EXECUTE') or die("Access Denied.");
?>
<section class="ccm-ui">
	<header><?=t('Page Permissions')?></header>
	<form method="post" action="<?=$controller->action('submit')?>" data-dialog-form="permissions" data-panel-detail-form="permissions">
		<?=Loader::helper('concrete/ui/help')->notify('panel', '/page/permissions')?>
		
		<p class="lead"><?=t('Who can view this page?')?></p>



		<?

		foreach ($gArray as $g) {
		?>

		<div class="radio"><label><input type="checkbox" name="readGID[]" value="<?=$g->getGroupID()?>" <? if (in_array($g->getGroupID(), $viewAccess)) { ?> checked <? } ?> /> <?=t($g->getGroupName())?></label></div>

		<? } ?>

		<hr/>

		<p class="lead"><?=t('Who can edit this page?')?></p>

		<?

		foreach ($gArray as $g) {
		?>

		<div class="radio"><label><input type="checkbox" name="editGID[]" value="<?=$g->getGroupID()?>" <? if (in_array($g->getGroupID(), $editAccess)) { ?> checked <? } ?> /> <?=t($g->getGroupName())?></label></div>

		<? } ?>

	</form>
	<div class="dialog-buttons ccm-panel-detail-form-actions">
		<button class="pull-left btn btn-default" type="button" data-dialog-actions="cancel" data-panel-detail-action="cancel"><?=t('Cancel')?></button>
		<button class="pull-right btn btn-success" type="button" data-dialog-action="submit" data-panel-detail-action="submit"><?=t('Save Changes')?></button>
	</div>
</section>