<?
defined('C5_EXECUTE') or die("Access Denied.");
?>

<section class="ccm-ui">
	<header><?=t('SEO')?></header>
	<form method="post" action="<?=$controller->action('submit')?>" class="ccm-panel-detail-content-form" data-panel-detail-form="seo">
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
	<div class="ccm-panel-detail-form-actions">
		<button class="pull-left btn btn-default" type="button" data-panel-detail-action="cancel"><?=t('Cancel')?></button>
		<button class="pull-right btn btn-success" type="button" data-panel-detail-action="submit"><?=t('Save Changes')?></button>
	</div>
</section>