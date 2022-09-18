<?php
defined('C5_EXECUTE') or die("Access Denied.");
$cmpp = new Permissions($pagetype);
$cp = new Permissions($page);
$v = $page->getVersionObject();
$publishDate = $v->getPublishDate();
?>

<?php if ($cp->canApprovePageVersions()) {

    $composer = Core::make('helper/concrete/composer');
    $publishTitle = $composer->getPublishButtonTitle($page);

    ?>

    <div class="float-end btn-group" data-page-type-composer-form-btns="publish">
        <button type="button" data-page-type-composer-form-btn="publish" class="ps-3 pe-3 btn btn-primary"><?=$publishTitle?></button>
        <button data-page-type-composer-form-btn="schedule" type="button" class="ps-3 pe-3 btn btn-primary <?php if ($publishDate) { ?>active<?php } ?>">
            <i class="fas fa-clock"></i>
        </button>
    </div>

    <div style="display: none">
        <div data-dialog="schedule-page">

            <?php $composer->displayPublishScheduleSettings($page); ?>

        </div>
    </div>

    <?php

} ?>

<?php if (!is_object($page) || $page->isPageDraft()) {
    ?>
    <button type="button" data-page-type-composer-form-btn="preview" class="btn btn-success float-end"><?=t('Edit Mode')?></button>
<?php
} else {
    ?>
    <button type="button" data-page-type-composer-form-btn="preview" class="btn btn-success float-end"><?=t('Save')?></button>
<?php
} ?>

<?php if (is_object($page) && $page->isPageDraft()) {
    if ($cp->canDeletePage()) {
        ?>
        <button type="button" data-page-type-composer-form-btn="discard" class="btn btn-danger float-start"><?=t('Discard Draft')?></button>
    <?php
    }
    ?>
    <button type="button" data-page-type-composer-form-btn="exit" class="btn btn-secondary float-start"><?=t('Save and Exit')?></button>
    <?php
} elseif ($cp->canDeletePage() && $cmpp->canAddPageType() && count($page->getCollectionChildrenArray(true)) === 0) {
    ?>
    <button type="button" data-page-type-composer-form-btn="revert" class="btn btn-danger float-left"><?=t('Revert to Draft')?></button>
    <div class="d-none">
        <div id="ccm-dialog-revert-page" class="ccm-ui">
            <form method="post" class="form-stacked">
                <?= Core::make("token")->output('revert_page') ?>
                <input type="hidden" name="cID" value="<?= h($page->getCollectionID()) ?>">
                <p><?= t('This will revert this page to draft page. This will delete this page from the sitemap then you can find it in the Drafts folder. This action cannot be undone. Are you sure?') ?></p>
            </form>
            <div class="dialog-buttons">
                <button class="btn btn-secondary float-start" onclick="jQuery.fn.dialog.closeTop()"><?= t('Cancel') ?></button>
                <button class="btn btn-danger float-end" data-dialog-revert-page="submit"><?= t('Revert Page to Draft') ?></button>
            </div>
        </div>
    </div>
    <?php
} ?>


<style type="text/css">
    div.ccm-ui button[data-page-type-composer-form-btn=save] {
        margin-left: 10px;
    }
    div.ccm-ui button[data-page-type-composer-form-btn=permissions] {
        margin-left: 10px;
    }
    div.ccm-ui button[data-page-type-composer-form-btn=exit] {
        margin-left: 10px;
    }
    div.ccm-ui button[data-page-type-composer-form-btn=preview] {
        margin-left: 10px;
    }
    div.ccm-ui div[data-page-type-composer-form-btns=publish] {
        margin-left: 10px;
    }
</style>

<script type="text/javascript">
    var ConcreteRevertPageToDraft = {
        sendRequest: function () {
            ConcreteEvent.fire('ExitComposerForm');

            $.ajax({
                type: 'post',
                dataType: 'json',
                data: $('#ccm-dialog-revert-page form').serialize(),
                url: '<?= URL::to('/ccm/system/panels/page/versions/revert') ?>',
                error: function (r) {
                    ConcreteAlert.dialog('Error', '<div class="alert alert-danger">' + r.responseText + '</div>');
                },
                success: function (r) {
                    if (r.error) {
                        ConcreteAlert.dialog('Error', '<div class="alert alert-danger">' + r.errors.join("<br>") + '</div>');
                    } else {
                        window.location.href = r.redirectURL;
                    }
                },
                complete: function () {
                    jQuery.fn.dialog.hideLoader();
                }
            })
        }
    }

    $(function() {
        $('button[data-page-type-composer-form-btn=schedule]').on('click', function() {
            jQuery.fn.dialog.open({
                element: 'div[data-dialog=schedule-page]',
                modal: true,
                width: 'auto',
                title: '<?=t('Schedule Publishing')?>',
                height: 'auto',
                onOpen: function() {
                    $('.ccm-check-in-schedule').on('click', function() {
                        var data = $('form[data-panel-detail-form=compose]').serializeArray();
                        var data = data.concat($('div[data-dialog=schedule-page] :input').serializeArray());
                        data.push({'name': 'action', 'value': 'schedule'});
                        ConcreteEvent.fire('PanelComposerPublish', {data: data});
                    });
                }
            });
        });
        $('button[data-page-type-composer-form-btn=revert]').on('click', function() {
            jQuery.fn.dialog.open({
                element: '#ccm-dialog-revert-page',
                modal: true,
                width: 400,
                title: '<?=t('Revert Page to Draft')?>',
                height: 'auto',
                onOpen: function() {
                    $('[data-dialog-revert-page=submit').on('click', function () {
                        ConcreteRevertPageToDraft.sendRequest();
                    });
                }
            });
        });
    });
</script>
