<?
defined('C5_EXECUTE') or die("Access Denied.");
$cmpp = new Permissions($pagetype);
?>

<button type="button" data-page-type-composer-form-btn="publish" class="btn btn-primary pull-right"><?=t('Publish')?></button>
<button type="button" data-page-type-composer-form-btn="save" class="btn pull-right"><?=t('Save and Exit')?></button>
<button type="button" data-page-type-composer-form-btn="exit" class="btn pull-right"><?=t('Back to Drafts')?></button>
<button type="button" data-page-type-composer-form-btn="discard" class="btn btn-danger pull-left"><?=t('Discard Draft')?></button>
<? if (PERMISSIONS_MODEL != 'simple' && $cmpp->canEditPageTypePermissions($pagetype)) { ?>
	<button type="button" data-page-type-composer-form-btn="permissions" class="btn pull-left" style="display: none"><?=t('Permissions')?></button>
<? } ?>


<style type="text/css">
	button[data-page-type-composer-form-btn=save] {
		margin-left: 10px;
	}
	button[data-page-type-composer-form-btn=permissions] {
		margin-left: 10px;
	}
	button[data-page-type-composer-form-btn=publish] {
		margin-left: 10px;
	}
</style>
