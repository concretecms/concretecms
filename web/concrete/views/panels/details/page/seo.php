<?
defined('C5_EXECUTE') or die("Access Denied.");
?>

<section class="ccm-ui">
	<header><?=t('SEO')?></header>
	<form method="post" action="<?=$controller->action('submit')?>" class="ccm-panel-detail-content-form" data-dialog-form="seo" data-panel-detail-form="seo">

	<? if ($allowEditPaths && !$c->isGeneratedCollection()) { ?>
	<div class="form-group">
		<label class="control-label launch-tooltip" data-placement="bottom" title="<?=t('This page must always be available from at least one URL. This is that URL.')?>" class="launch-tooltip"><?=t('URL Slug')?></label>
		<div>
			<input type="text" class="form-control" name="cHandle" value="<?php echo $c->getCollectionHandle()?>" id="cHandle"><input type="hidden" name="oldCHandle" id="oldCHandle" value="<?php echo $c->getCollectionHandle()?>">
		</div>
	</div>
	<? } ?>

	<? foreach($attributes as $ak) { ?>
		<? $av = $c->getAttributeValueObject($ak); ?>
		<div class="form-group">
			<label class="control-label"><?=$ak->getAttributeKeyName()?></label>
			<div>
			<?=$ak->render('form', $av); ?>
			</div>
		</div>
	<? } ?>


	</form>
	<div class="ccm-panel-detail-form-actions dialog-buttons">
		<button class="pull-left btn btn-default" type="button" data-dialog-action="cancel" data-panel-detail-action="cancel"><?=t('Cancel')?></button>
		<button class="pull-right btn btn-success" type="button" data-dialog-action="submit" data-panel-detail-action="submit"><?=t('Save Changes')?></button>
	</div>
</section>