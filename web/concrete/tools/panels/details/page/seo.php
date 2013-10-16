<?
defined('C5_EXECUTE') or die("Access Denied.");
$c = Page::getByID(Loader::helper('security')->sanitizeInt($_REQUEST['cID']));
if (is_object($c) && !$c->isError()) {
	$cp = new Permissions($c);
	if ($cp->canEditPageContents()) {
		$as = AttributeSet::getByHandle('seo');
		$attributes = $as->getAttributeKeys();
		if ($_POST['submitPanelDetailForm']) {
			$nvc = $c->getVersionToModify();
			foreach($attributes as $ak) {
				$ak->saveAttributeForm($nvc);
			}
			$r = new PageEditResponse($e);
			$r->setPage($c);
			$r->setMessage(t('The SEO information has been saved.'));
			$r->outputJSON();
		}

		?>

		<section class="ccm-ui">
			<header><?=t('SEO')?></header>
			<form method="post" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/panels/details/page/seo" class="ccm-panel-detail-content-form" data-panel-detail-form="seo">
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

	<? }
}