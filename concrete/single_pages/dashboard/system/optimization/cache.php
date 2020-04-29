<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Controller\SinglePage\Dashboard\System\Optimization\Cache $controller
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var Concrete\Core\Localization\Service\Date $dateService
 * @var bool $enableBlockCache
 * @var bool $enableThemeCssCache
 * @var bool $compressThemePreprocessorOutput
 * @var bool $generateLessSourcemap
 * @var bool $enableOverrideCache
 * @var string $fullPageCacheGlobal
 * @var string $fullPageCacheLifetime
 * @var int $defaultCacheLifetime
 * @var int|null $fullPageCacheCustomLifetime
 */

?>
<form method="post" action="<?= $controller->action('update_cache') ?>">
    <?php $token->output('update_cache') ?>

    <fieldset>
        <legend>
            <?= t('Block Cache') ?>
            <span class="launch-tooltip" data-placement="right" title="<?= t('Stores the output of blocks which support block caching') ?>"><i class="fas fa-question-circle"></i></span>
        </legend>
        <div class="form-group">
            <div class="radio">
                <label>
                    <?= $form->radio('ENABLE_BLOCK_CACHE', '0', $enableBlockCache ? '1' : '0') ?>
                    <?= t('Off - Good for development of custom blocks.') ?>
                </label>
            </div>
            <div class="radio">
                <label>
                    <?= $form->radio('ENABLE_BLOCK_CACHE', '1', $enableBlockCache ? '1' : '0') ?>
                    <?= t('On - Helps speed up a live site.') ?>
                </label>
            </div>
        </div>
    </fieldset>

    <fieldset>
        <legend>
            <?= t('Theme CSS Cache') ?>
            <span class="launch-tooltip" data-placement="right" title="<?= t('Caches the output of customized theme stylesheets for faster loading. Turn off if you are editing LESS files in your theme directly.') ?>"><i class="fas fa-question-circle"></i></span>
        </legend>
        <div class="form-group">
            <div class="radio">
                <label>
                    <?= $form->radio('ENABLE_THEME_CSS_CACHE', '0', $enableThemeCssCache ? '1' : '0') ?>
                    <?= t('Off - Good for active theme development when using LESS files.') ?>
                </label>
            </div>
            <div class="radio">
                <label>
                    <?= $form->radio('ENABLE_THEME_CSS_CACHE', '1', $enableThemeCssCache ? '1' : '0') ?>
                    <?= t('On - Helps speed up a live site.') ?>
                </label>
            </div>
        </div>
    </fieldset>

    <fieldset>
        <legend>
            <?= t('Compress LESS Output') ?>
            <span class="launch-tooltip" data-placement="right" title="<?= t('Determines whether compiled LESS stylesheets should output as compressed CSS. Uncompressed stylesheets are slightly larger but easier to read.') ?>"><i class="fas fa-question-circle"></i></span>
        </legend>
        <div class="form-group">
            <div class="radio">
                <label>
                    <?= $form->radio('COMPRESS_THEME_PREPROCESSOR_OUTPUT', '0', $compressThemePreprocessorOutput ? '1' : '0') ?>
                    <?= t('Off - Good for debugging generated CSS output.') ?>
                </label>
            </div>
            <div class="checkbox ml-4">
                <label>
                    <?= $form->checkbox('GENERATE_LESS_SOURCEMAP', '1', $generateLessSourcemap) ?>
                    <?= t('enable source maps in generated CSS files') ?>
                </label>
            </div>
            <div class="radio">
                <label>
                    <?= $form->radio('COMPRESS_THEME_PREPROCESSOR_OUTPUT', '1', $compressThemePreprocessorOutput ? '1' : '0') ?>
                    <?= t('On - Helps speed up a live site.') ?>
                </label>
            </div>
        </div>
    </fieldset>

    <fieldset>
        <legend><?= t('Overrides Cache') ?>
            <span class="launch-tooltip" data-placement="right" title="<?= t('Stores the location and existence of source code files.') ?>"><i class="fas fa-question-circle"></i></span>
        </legend>
        <div class="form-group">
            <div class="radio">
                <label>
                    <?= $form->radio('ENABLE_OVERRIDE_CACHE', '0', $enableOverrideCache ? '1' : '0') ?>
                    <?= t('Off - Good for development.') ?>
                </label>
            </div>
            <div class="radio">
                <label>
                    <?= $form->radio('ENABLE_OVERRIDE_CACHE', '1', $enableOverrideCache ? '1' : '0') ?>
                    <?= t('On - Helps speed up a live site.') ?>
                </label>
            </div>
        </div>
    </fieldset>

    <fieldset>
        <legend>
            <?= t('Full Page Caching') ?>
            <span class="launch-tooltip" data-placement="right" title="<?= t('Stores the output of an entire page.') ?>"><i class="fas fa-question-circle"></i></span>
        </legend>
        <div class="form-group">
            <div class="radio">
                <label>
                    <?= $form->radio('FULL_PAGE_CACHE_GLOBAL', '0', $fullPageCacheGlobal ?: '0') ?>
                    <?= t('Off - Turn it on by hand for specific pages.') ?>
                </label>
            </div>
            <div class="radio">
                <label>
                    <?= $form->radio('FULL_PAGE_CACHE_GLOBAL', 'blocks', $fullPageCacheGlobal) ?>
                    <?= t('On - If blocks on the particular page allow it.') ?>
                </label>
            </div>
            <div class="radio">
                <label>
                    <?= $form->radio('FULL_PAGE_CACHE_GLOBAL', 'all', $fullPageCacheGlobal) ?>
                    <?= t('On - In all cases.') ?>
                </label>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label"><?= t('Expire Pages from the Cache') ?></label>
            <div class="radio">
                <label>
                    <?= $form->radio('FULL_PAGE_CACHE_LIFETIME', 'default', $fullPageCacheLifetime) ?>
                    <?= t('Every %s (default setting).', $dateService->describeInterval($defaultCacheLifetime)) ?>
                </label>
            </div>
            <div class="radio">
                <label>
                    <?= $form->radio('FULL_PAGE_CACHE_LIFETIME', 'forever', $fullPageCacheLifetime) ?>
                    <?= t('Only when manually removed or the cache is cleared.') ?>
                </label>
            </div>
            <div class="radio">
                <label>
                    <?= $form->radio('FULL_PAGE_CACHE_LIFETIME', 'custom', $fullPageCacheLifetime) ?>
                    <?= t(
    'Every %s minutes',
    $form->number('FULL_PAGE_CACHE_LIFETIME_CUSTOM', $fullPageCacheCustomLifetime, ['min' => 1, 'class' => 'd-inline form-control-sm', 'style' => 'width: 5rem'])
) ?>
                </label>
            </div>
        </div>
    </fieldset>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button class="float-right btn btn-primary" type="submit" ><?= t('Save') ?></button>
        </div>
    </div>

</form>

<script>
$(document).ready(function() {
    'use strict';
    function fullPageCacheLifetimeChanged() {
        $('#FULL_PAGE_CACHE_LIFETIME_CUSTOM').attr('disabled', $('input[name="FULL_PAGE_CACHE_LIFETIME"]:checked').val() !== 'custom');
    }
    $('input[name="FULL_PAGE_CACHE_LIFETIME"]').on('change', function() {
        fullPageCacheLifetimeChanged();
    });
    $('input[name="FULL_PAGE_CACHE_LIFETIME"][value="custom"]').on('click', function() {
        setTimeout(function() { $('#FULL_PAGE_CACHE_LIFETIME_CUSTOM').focus(); }, 0);
    });
    fullPageCacheLifetimeChanged();
});
</script>
