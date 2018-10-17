<?php

use Concrete\Core\View\View;

defined('C5_EXECUTE') or die('Access Denied.');

/* @var Concrete\Controller\Dialog\Page\DragRequest $controller */
/* @var Concrete\Core\View\DialogView $view */

/* @var bool|null $showProgressBar */
/* @var int $totalItems */
// -- OR --
/* @var string $validationToken */
/* @var Concrete\Core\Page\Sitemap\DragRequestData $dragRequestData */
/* @var string $originalPageIDs */

if (isset($showProgressBar) && $showProgressBar) {
    View::element('progress_bar', ['totalItems' => $totalItems, 'totalItemsSummary' => t2('%d page', '%d pages', $totalItems)]);

    return;
}
if (!$dragRequestData->canDoAnyOf([$dragRequestData::OPERATION_MOVE, $dragRequestData::OPERATION_ALIAS, $dragRequestData::OPERATION_COPY, $dragRequestData::OPERATION_COPYVERSION])) {
    ?>
    <div class="alert alert-danger">
        <?= t('You are not allowed to perform any operation with the selected pages.') ?>
    </div>
    <?php
    return;
}
$singleOriginalPage = $dragRequestData->getSingleOriginalPage();
?>

<div class="alert alert-info">
    <?php
    if ($singleOriginalPage === null) {
        echo t('What do you wish to do?');
    } else {
        echo t('You dragged "%s" onto "%s". What do you wish to do?', h($singleOriginalPage->getCollectionName()), h($dragRequestData->getDestinationPage()->getCollectionName()));
    }
    ?>
</div>

<form>
    <input type="hidden" name="validationToken" id="validationToken" value="<?= h($validationToken) ?>" />
    <input type="hidden" name="dragMode" id="dragMode" value="<?= h($dragRequestData->getDragMode()) ?>" />
    <input type="hidden" name="destCID" id="destCID" value="<?= $dragRequestData->getDestinationPage()->getCollectionID() ?>" />
    <?php
    if ($dragRequestData->getDestinationSibling() !== null) {
        ?>
        <input type="hidden" name="destSibling" id="destSibling" value="<?= $dragRequestData->getDestinationSibling()->getCollectionID() ?>" />
        <?php
    }
    ?>
    <input type="hidden" name="origCID" id="origCID" value="<?= $originalPageIDs ?>" />

    <?php
    if ($dragRequestData->canDo($dragRequestData::OPERATION_MOVE)) {
        ?>
        <div class="form-group">
            <div class="radio">
                <label>
                    <input type="radio" id="ctaskMove" name="ctask" value="<?= $dragRequestData::OPERATION_MOVE ?>" checked="checked" />
                    <?php
                    if ($singleOriginalPage !== null) {
                        echo t('<strong>Move</strong> "%1$s" beneath "%2$s"', h($singleOriginalPage->getCollectionName()), h($dragRequestData->getDestinationPage()->getCollectionName()));
                     } else {
                         echo t('<strong>Move</strong> pages beneath "%s"', h($dragRequestData->getDestinationPage()->getCollectionName()));
                     }
                     ?>
                </label>
                <div class="checkbox" style="margin: 0 0 0 20px">
                    <label>
                        <input type="checkbox" id="saveOldPagePath" name="saveOldPagePath" value="1" />
                        <?= t('Save old page path') ?>
                    </label>
                </div>
            </div>
        </div>
        <?php
    }

    if ($dragRequestData->canDo($dragRequestData::OPERATION_ALIAS)) {
        ?>
        <div class="form-group">
            <div class="radio">
                <label>
                    <input type="radio" id="ctaskAlias" name="ctask" value="<?= $dragRequestData::OPERATION_ALIAS ?>" />
                    <?php
                    if ($singleOriginalPage !== null) {
                        echo t('<strong>Alias</strong> "%1$s" beneath "%2$s"', h($singleOriginalPage->getCollectionName()), h($dragRequestData->getDestinationPage()->getCollectionName()));
                    } else {
                        echo t('<strong>Alias</strong> pages beneath "%s"', h($dragRequestData->getDestinationPage()->getCollectionName()));
                    }
                    ?>
                </label>
                <div class="text-muted" style="margin: 0 0 0 20px">
                    <?= t('Pages appear in both locations; all edits to originals will be reflected in their alias.') ?>
                </div>
            </div>
        </div>
        <?php
    }

    if ($dragRequestData->canDo($dragRequestData::OPERATION_COPY)) {
        ?>
        <div class="form-group">
            <div class="radio">
                <label>
                    <input type="radio" id="ctaskCopy" name="ctask" value="<?= $dragRequestData::OPERATION_COPY ?>" />
                    <?php
                    if ($singleOriginalPage !== null) {
                        echo t('<strong>Copy</strong> "%1$s" beneath "%2$s"', h($singleOriginalPage->getCollectionName()), h($dragRequestData->getDestinationPage()->getCollectionName()));
                    } else {
                        echo t('<strong>Copy</strong> pages beneath "%s"', h($dragRequestData->getDestinationPage()->getCollectionName()));
                    }
                    ?>
                </label>
                <?php
                if ($dragRequestData->canDo($dragRequestData::OPERATION_COPYALL)) {
                    ?>
                    <div class="checkbox" style="margin: 0 0 0 20px">
                        <label class="text-muted">
                            <input type="radio" id="copyThisPage" name="copyAll" value="0" disabled="disabled" checked="checked" />
                            <?= t('Copy page.') ?>
                        </label>
                    </div>
                    <div class="checkbox" style="margin: 0 0 0 20px">
                        <label class="text-muted">
                            <input type="radio" id="copyChildren" name="copyAll" value="1" disabled="disabled" />
                            <?= t('Copy page + children.') ?>
                        </label>
                    </div>
                    <?php
                } else {
                    ?>
                    <div class="text-muted" style="margin: 0 0 0 20px">
                        <?= t('Your copy operation will only affect the current page - not any children.') ?>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
        <?php
    }

    if ($dragRequestData->canDo($dragRequestData::OPERATION_COPYVERSION)) {
        ?>
        <div class="form-group">
            <div class="radio">
                <label>
                    <input type="radio" id="ctaskCopyVersion" name="ctask" value="<?= $dragRequestData::OPERATION_COPYVERSION ?>" />
                    <?= t('<strong>Copy Version</strong> of "%1$s" to "%2$s"', h($singleOriginalPage->getCollectionName()), h($dragRequestData->getDestinationPage()->getCollectionName())) ?>
                </label>
                <div class="text-muted" style="margin: 0 0 0 20px">
                    <?= t('The most recent page version of "%1$s" will be copied to "%2$s".', h($singleOriginalPage->getCollectionName()), h($dragRequestData->getDestinationPage()->getCollectionName())) ?>
                </div>
            </div>
        </div>
        <?php
    }
    ?>

    <div class="dialog-buttons">
        <a href="javascript:void(0)" onclick="$.fn.dialog.closeTop()" class="pull-left btn btn-default"><?= t('Cancel') ?></a>
        <a href="javascript:void(0)" onclick="ConcreteSitemap.submitDragRequest()" class="pull-right btn btn-primary"><?= t('Go') ?></a>
    </div>
</form>

<script>
$(function() {
    function setDisabled(id, disabled) {
        var $input = $('#' + id),
            $label = $input.closest('label');
        if (disabled) {
            $input.attr('disabled', 'disabled');
            $label.addClass('text-muted');
        } else {
            $input.removeAttr('disabled');
            $label.removeClass('text-muted');
        }
    }
    $('#ctaskMove,#ctaskAlias,#ctaskCopy,#ctaskCopyVersion').on('click', function() {
        setDisabled('saveOldPagePath', this.id !== 'ctaskMove');
        setDisabled('copyThisPage', this.id !== 'ctaskCopy');
        setDisabled('copyChildren', this.id !== 'ctaskCopy');
    });

    if (window.localStorage && window.localStorage.getItem && window.localStorage.removeItem) {
        if (window.localStorage.getItem('ccm-sitemap-movePageSaveOldPagePath')) {
            $('#saveOldPagePath').prop('checked', true);
        }
        $('#saveOldPagePath').on('click', function() {
            if (this.checked) {
                window.localStorage.setItem('ccm-sitemap-movePageSaveOldPagePath', '1');
            } else {
                window.localStorage.removeItem('ccm-sitemap-movePageSaveOldPagePath');
            }
        });
    }
});
</script>
