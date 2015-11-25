<?
defined('C5_EXECUTE') or die("Access Denied.");
$v = $c->getVersionObject();
?>

<div class="ccm-panel-content-inner">

<form method="post" id="ccm-check-in" action="<?=$controller->action('submit')?>">

<h5><?=t('Version Comments')?></h5>

<div class="ccm-panel-check-in-comments"><textarea name="comments" id="ccm-check-in-comments" /></textarea></div>

<? if ($cp->canApprovePageVersions()) {
	if ($c->isPageDraft()) {
		$publishTitle = t('Publish Page');
	} else {
		$publishTitle = t('Publish Changes');
		$pk = PermissionKey::getByHandle('approve_page_versions');
		$pk->setPermissionObject($c);
		$pa = $pk->getPermissionAccessObject();
        $workflows = array();
        $canApproveWorkflow = true;
        if (is_object($pa)) {
            $workflows = $pa->getWorkflows();
        }
        foreach($workflows as $wf) {
            if (!$wf->canApproveWorkflow()) {
                $canApproveWorkflow = false;
            }
        }

        if (count($workflows > 0) && !$canApproveWorkflow) {
            $publishTitle = t('Submit to Workflow');
        }
        
	}
?>
<div class="ccm-panel-check-in-publish">

	<? /*
    <div class="btn-group">
		<button id="ccm-check-in-publish" type="submit" name="action" value="publish" class="btn btn-primary"><?=$publishTitle?></button>
		<button id="ccm-check-in-publish-time" type="button" class="btn btn-primary"><i class="fa fa-clock-o fa-inverse"></i></button>
	</div>
    */?>

    <button <? if (is_object($publishErrors) && $publishErrors->has()) { ?>disabled<? } ?>
            id="ccm-check-in-publish" type="submit" name="action" value="publish" class="btn-block btn btn-primary"><?=$publishTitle?></button>
    <br/>
    <? if (is_object($publishErrors) && $publishErrors->has()) { ?>
        <div class="small">
        <? foreach($publishErrors->getList() as $error) { ?>
            <div class="text-warning"><strong><i class="fa fa-warning"></i> <?=$error?></strong></div>
            <br/>
        <? } ?>
        </div>

        <?
        $pagetype = PageType::getByID($c->getPageTypeID());
        if (is_object($pagetype)) { ?>
            <div class="small">
           <div class="text-info"><strong><i class="fa fa-question-circle"></i>
            <?=t('You can specify page name, page location and attributes from the <a href="#" data-launch-panel-detail="page-composer" data-panel-detail-url="%s" data-panel-transition="fade">Page Compose interface</a>.', URL::to('/ccm/system/panels/details/page/composer'))?>
            </strong></div>
            <br/>
            </div>
        <? } ?>
    <? } ?>
</div>

<? } ?>

	<button id="ccm-check-in-preview" type="submit" name="action" value="save" class="btn-block btn-success btn"><?=t('Save Changes')?></button>

    <? if ($c->isPageDraft() && $cp->canDeletePage()) { ?>
		<button id="ccm-check-in-discard" type="submit" name="action" value="discard" class="btn-block btn-danger btn"><?=t('Discard Draft')?></button>
	<? } else if ($v->canDiscard()) { ?>
		<button id="ccm-check-in-discard" type="submit" name="action" value="discard" class="btn-block btn-danger btn"><?=t('Discard Changes')?></button>
	<? } ?>
	<input type="hidden" name="approve" value="PREVIEW" id="ccm-approve-field" />

</form>

<script type="text/javascript">
$(function() {
    setTimeout("$('#ccm-check-in-comments').focus();",300);
    $('#ccm-check-in').concreteAjaxForm();
    <?php if ($c->isPageDraft() && $cp->canDeletePage()) { ?>
    $('button#ccm-check-in-discard').on('click', function () {
        return confirm('<?=t('This will remove this draft and it cannot be undone. Are you sure?')?>');
    });
	<?php } ?>
});
</script>

</div>