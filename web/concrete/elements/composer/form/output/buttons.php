<?
defined('C5_EXECUTE') or die("Access Denied.");
$cmpp = new Permissions($composer);
?>

<button type="button" data-composer-btn="publish" class="btn btn-primary pull-right"><?=t('Publish')?></button>
<button type="button" data-composer-btn="save" class="btn pull-right"><?=t('Save and Exit')?></button>
<button type="button" data-composer-btn="exit" class="btn pull-right"><?=t('Back to Drafts')?></button>
<button type="button" data-composer-btn="discard" class="btn btn-danger pull-left"><?=t('Discard Draft')?></button>
<? if (PERMISSIONS_MODEL != 'simple' && $cmpp->canEditComposerPermissions($composer)) { ?>
	<button type="button" data-composer-btn="permissions" class="btn pull-left" style="display: none"><?=t('Permissions')?></button>
<? } ?>


<style type="text/css">
	button[data-composer-btn=save] {
		margin-left: 10px;
	}
	button[data-composer-btn=permissions] {
		margin-left: 10px;
	}
	button[data-composer-btn=publish] {
		margin-left: 10px;
	}
</style>
