<?
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div class="ccm-panel-content-inner">

<form method="post" id="ccm-check-in" action="<?=$controller->action('submit')?>">

<h5><?=t('Version Comments')?></h5>

<div class="ccm-panel-check-in-comments"><textarea name="comments" id="ccm-check-in-comments" /></textarea></div>

<? if ($cp->canApprovePageVersions()) {
	$publishTitle = t('Publish Changes');
	$pk = PermissionKey::getByHandle('approve_page_versions');
	$pk->setPermissionObject($c);
	$pa = $pk->getPermissionAccessObject();
	if (is_object($pa) && count($pa->getWorkflows()) > 0) {
		$publishTitle = t('Submit to Workflow');
	}
?>
<div class="ccm-panel-check-in-publish">

	<div class="btn-group">
		<button id="ccm-check-in-publish" type="button" class="btn btn-primary"><?=$publishTitle?></button>
		<button id="ccm-check-in-publish-time" type="button" class="btn btn-primary"><i class="glyphicon glyphicon-time"></i></button>
	</div>

</div>

<? } ?>

<div class="ccm-panel-check-in-preview">
	<button id="ccm-check-in-preview" type="button" class="btn-success btn"><?=t('Save Changes')?></button>
	<button id="ccm-check-in-discard" type="button" class="btn-danger btn"><?=t('Discard Changes')?></button>
	<input type="hidden" name="approve" value="PREVIEW" id="ccm-approve-field" />
</div>

</form>

<script type="text/javascript">
$(function() {
    setTimeout("$('#ccm-check-in-comments').focus();",300);
    $("#ccm-check-in-preview").click(function() {
        $("#ccm-approve-field").val('PREVIEW');
        $("#ccm-check-in").submit();
    });

    $("#ccm-check-in-discard").click(function() {
        $("#ccm-approve-field").val('DISCARD');
        $("#ccm-check-in").submit();
    });

    $("#ccm-check-in-publish").click(function() {
        $("#ccm-approve-field").val('APPROVE');
        $("#ccm-check-in").submit();
    });
});
</script>

</div>