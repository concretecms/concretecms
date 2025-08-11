<?php

defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="ccm-ui">

    <?php
    if (count($pages) == 0) {
        ?>
        <?= t("You have not selected any pages.");
        ?>
        <?php
    } else {
        ?>

        <form action="<?= $controller->action('submit') ?>" data-dialog-form="bulk-page-caching" id="ccm-bulk-page-caching-form">

            <?php foreach ($pages as $c) {
                $cp = new Permissions($c);
                if ($cp->canEditPageSpeedSettings()) { ?>
                    <input type="hidden" name="item[]" value="<?=$c->getCollectionID()?>">
            <?php }
            }
            ?>

            <div class="mb-3">
                <label class="form-label"><?= t('Full Page Caching') ?></label>
                <?php if ($fullPageCaching == '-2') { ?>
                    <div class="form-check">
                        <input type="radio" name="cCacheFullPageContent" id="cacheOption1" value="-2"
                               class="form-check-input" <?= $fullPageCaching == -2 ? 'checked' : '' ?>>
                        <label class="form-check-label" for="cacheOption1">
                            <?= t('Multiple values') ?>
                        </label>
                    </div>
                <?php } ?>
                <div class="form-check">
                    <input type="radio" name="cCacheFullPageContent" id="cacheOption2" value="-1"
                           class="form-check-input" <?= $fullPageCaching == -1 ? 'checked' : '' ?>>
                    <label class="form-check-label" for="cacheOption2">
                        <?= t('Use global setting - %s', $globalSetting) ?>
                    </label>
                </div>
                <div class="form-check">
                    <input type="radio" name="cCacheFullPageContent" id="cacheOption3" value="0"
                           class="form-check-input" <?= $fullPageCaching == 0 ? 'checked' : '' ?>>
                    <label class="form-check-label" for="cacheOption3">
                        <?= t('Do not cache this page.') ?>
                    </label>
                </div>
                <div class="form-check">
                    <input type="radio" name="cCacheFullPageContent" id="cacheOption4" value="1"
                           class="form-check-input" <?= $fullPageCaching == 1 ? 'checked' : '' ?>>
                    <label class="form-check-label" for="cacheOption4">
                        <?= t('Cache this page.') ?>
                    </label>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label"><?= t('Cache for how long?') ?></label>
                <?php
                $val = ($cCacheFullPageContentOverrideLifetimeCustomValue > 0 && $cCacheFullPageContentOverrideLifetime) ? $cCacheFullPageContentOverrideLifetimeCustomValue : ''; ?>

                <?php if ($cCacheFullPageContentOverrideLifetime == '-1') { ?>
                    <div class="form-check">
                        <input type="radio" name="cCacheFullPageContentOverrideLifetime" id="lifetimeOption1"
                               value="-1"
                               class="form-check-input" <?= $cCacheFullPageContentOverrideLifetime == -1 ? 'checked' : '' ?>>
                        <label class="form-check-label" for="lifetimeOption1">
                            <?= t('Multiple values') ?>
                        </label>
                    </div>
                <?php } ?>
                <div class="form-check">
                    <input type="radio" name="cCacheFullPageContentOverrideLifetime" id="lifetimeOption2"
                           value="default"
                           class="form-check-input" <?= $cCacheFullPageContentOverrideLifetime == 0 ? 'checked' : '' ?>>
                    <label class="form-check-label" for="lifetimeOption2">
                        <?= t('Use global setting - %s', $globalSettingLifetime) ?>
                    </label>
                </div>
                <div class="form-check">
                    <input type="radio" name="cCacheFullPageContentOverrideLifetime" id="lifetimeOption4"
                           value="forever"
                           class="form-check-input" <?= $cCacheFullPageContentOverrideLifetime == 'forever' ? 'checked' : '' ?>>
                    <label class="form-check-label" for="lifetimeOption4">
                        <?= t('Until manually cleared') ?>
                    </label>
                </div>
                <div class="form-check">
                    <input type="radio" name="cCacheFullPageContentOverrideLifetime" id="lifetimeOption5"
                           value="custom"
                           class="form-check-input" <?= $cCacheFullPageContentOverrideLifetime == 'custom' ? 'checked' : '' ?>>
                    <label class="form-check-label" for="lifetimeOption5">
                        <?= t('Custom') ?>
                    </label>
                    <div class="row row-cols-auto g-0 align-items-center">
                        <div class="col-auto">
                            <input type="text" name="cCacheFullPageContentLifetimeCustom"
                                   id="customLifetime" value="<?= $val ?>" min="1" class="form-control"
                                   style="width: 110px; display: inline-block;">
                        </div>
                        <div class="col-auto">
                            <span class="ms-1"><?= t('minutes') ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="dialog-buttons d-flex justify-content-end">
                <button class="btn btn-secondary me-2" type="button" data-dialog-action="cancel"
                        data-panel-detail-action="cancel"><?= t('Cancel') ?></button>
                <button class="btn btn-success" type="button" id="save-bulk-cache-settings-button" data-dialog-action="submit"
                        data-panel-detail-action="submit"><?= t('Save Changes') ?></button>
            </div>
        </form>

        <?php
    }
    ?>
</div>

<script type="text/javascript">
    $(function () {
        $('#ccm-bulk-page-caching-form').on('change', 'input[name=cCacheFullPageContent]', function() {
            let cCacheFullPageContent = $('input[name=cCacheFullPageContent]:checked').val()
            if (cCacheFullPageContent === '1') {
                $('input[name=cCacheFullPageContentOverrideLifetime]').prop('disabled', false);
                if ($('input[name=cCacheFullPageContentOverrideLifetime][value="-1"]').length) {
                    $('input[name=cCacheFullPageContentOverrideLifetime][value="-1"]').prop('disabled', true);
                    $('input[name=cCacheFullPageContentOverrideLifetime][value=default]').prop('checked', true);
                }
            } else {
                $('input[name=cCacheFullPageContentOverrideLifetime]').prop('disabled', true);
                if ($('input[name=cCacheFullPageContentOverrideLifetime][value="-1"]').length) {
                    $('input[name=cCacheFullPageContentOverrideLifetime][value="-1"]').prop('checked', true);
                } else {
                    $('input[name=cCacheFullPageContentOverrideLifetime][value="default"]').prop('checked', true);
                }
            }
            if (cCacheFullPageContent === '-2') {
                $('#save-bulk-cache-settings-button').prop('disabled', true)
            } else {
                $('#save-bulk-cache-settings-button').prop('disabled', false)
            }
            $('input[name=cCacheFullPageContentOverrideLifetime]:checked').trigger('change')
        })
        $('#ccm-bulk-page-caching-form').on('change', 'input[name=cCacheFullPageContentOverrideLifetime]', function() {
            let cCacheFullPageContentOverrideLifetime = $('input[name=cCacheFullPageContentOverrideLifetime]:checked').val()
            if (cCacheFullPageContentOverrideLifetime === 'custom') {
                $('input[name=cCacheFullPageContentLifetimeCustom]').prop('disabled', false);
            } else {
                $('input[name=cCacheFullPageContentLifetimeCustom]').prop('disabled', true);
                $('input[name=cCacheFullPageContentLifetimeCustom]').val('');
            }
        })
        $('input[name=cCacheFullPageContent]:checked').trigger('change')
    });
</script>

