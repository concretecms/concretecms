<?php defined('C5_EXECUTE') or die("Access Denied.");
$v = $c->getVersionObject();
$datetime = loader::helper('form/date_time');
?>

<div class="ccm-panel-content-inner">

<form method="post" id="ccm-check-in" action="<?=$controller->action('submit')?>">

<h5><?=t('Version Comments')?></h5>

<div class="ccm-panel-check-in-comments"><textarea name="comments" id="ccm-check-in-comments" /></textarea></div>

<?php if ($cp->canApprovePageVersions()) {
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
        foreach ($workflows as $wf) {
            if (!$wf->canApproveWorkflow()) {
                $canApproveWorkflow = false;
            }
        }

        if (count($workflows) > 0 && !$canApproveWorkflow) {
            $publishTitle = t('Submit to Workflow');
        }
    }
    ?>
<div class="ccm-panel-check-in-publish">
    <?php $publishAction = (is_object($publishErrors) && $publishErrors->has()) ? false : true ?>
    <div class="btn-group">
        <button id="ccm-check-in-publish" type="submit" name="action" value="publish"
                class="btn btn-primary" <?=$publishAction ?: 'disabled' ?>>
            <?=$publishTitle?>
        </button>
        <button id="ccm-check-in-schedule" type="button" class="btn btn-primary" <?= $publishAction ?: 'disabled' ?>>
            <i class="fa fa-clock-o"></i>
        </button>
    </div>
    <?php if ($canApproveWorkflow): ?>
        <div id="ccm-check-in-schedule-wrapper">
            <h5>Scheduled Publish</h5>
            <a href="#" class="remove">Remove</a>
            <div class="form-group datetime-group">
                <?= $datetime->datetime('check-in-scheduler', $publishDate, false, true,
                    'check-in-scheduler-calendar'); ?>
            </div>
            <div class="form-group submit-group">
                <span class="timezone"><?=$timezone ?></span>
                <button type="submit" name="action" value="schedule"
                        class="btn btn-primary ccm-check-in-schedule " <?=$publishAction ?: 'disabled' ?>>
                    Schedule
                </button>
            </div>
        </div>
    <?php endif ?>
    <br/>
    <?php if ($publishAction): ?>
        <div class="small">
        <?php foreach ($publishErrors->getList() as $error): ?>
            <div class="text-warning"><strong><i class="fa fa-warning"></i> <?=$error?></strong></div>
            <br/>
        <?php endforeach; ?>
        </div>

        <?php $pagetype = PageType::getByID($c->getPageTypeID()); ?>
        <?php if (is_object($pagetype)): ?>
            <div class="small">
                <div class="text-info">
                    <strong>
                        <i class="fa fa-question-circle"></i>
                        <?=t('You can specify page name, page location and attributes from the ' .
                             '<a href="#" data-launch-panel-detail="page-composer" data-panel-detail-url="%s" ' .
                             'data-panel-transition="fade">Page Compose interface</a>.',
                             URL::to('/ccm/system/panels/details/page/composer')); ?>
                    </strong>
                </div>
                <br/>
            </div>
        <?php endif ?>
    <?php endif ?>
</div>

<?php 
} ?>

	<button id="ccm-check-in-preview" type="submit" name="action" value="save" class="btn-block btn-success btn"><?=t('Save Changes')?></button>

    <?php if ($c->isPageDraft() && $cp->canDeletePage()) {
    ?>
		<button id="ccm-check-in-discard" type="submit" name="action" value="discard" class="btn-block btn-danger btn"><?=t('Discard Draft')?></button>
	<?php 
} elseif ($v->canDiscard()) {
    ?>
		<button id="ccm-check-in-discard" type="submit" name="action" value="discard" class="btn-block btn-danger btn"><?=t('Discard Changes')?></button>
	<?php 
} ?>
	<input type="hidden" name="approve" value="PREVIEW" id="ccm-approve-field" />

</form>

<script type="text/javascript">
$(function() {
    setTimeout("$('#ccm-check-in-comments').focus();",300);
    $('#ccm-check-in').concreteAjaxForm();
    <?php if ($c->isPageDraft() && $cp->canDeletePage()) {
    ?>
    $('button#ccm-check-in-discard').on('click', function () {
        return confirm('<?=t('This will remove this draft and it cannot be undone. Are you sure?')?>');
    });
	<?php 
} ?>

    var toggleScheduler = function () {
        $("#ccm-check-in-schedule-wrapper").toggle();
        $("#ccm-check-in-publish").prop('disabled', !$("#ccm-check-in-publish").prop('disabled'));
    },
        isScheduled = <?= json_encode($publishDate) ?>;

    if (isScheduled) {
        toggleScheduler();
    }

    $("#ccm-check-in-schedule, #ccm-check-in-schedule-wrapper .remove").click(function () {
        toggleScheduler();
    });
});
</script>

</div>