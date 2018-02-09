<?php
defined('C5_EXECUTE') or die('Access Denied.');

/* @var Concrete\Core\Page\View\PageView $view */
/* @var Concrete\Core\Validation\CSRF\Token $token */
/* @var Concrete\Core\Form\Service\Form $form */

/* @var bool $restrict_uploaded_image_sizes */
/* @var int $restrict_max_width */
/* @var int $restrict_max_height */

?>

<form method="POST" action="<?= $view->action('save') ?>">
    <?= $token->output('image_uploading') ?>

    <div class="checkbox">
        <label>
            <?= $form->checkbox('restrict_uploaded_image_sizes', '1', $restrict_uploaded_image_sizes) ?>
            <span><?=t('Automatically resize uploaded images')?></span>
        </label>
    </div>

    <div id="resizing-values"<?= $restrict_uploaded_image_sizes ? '' : ' style="display: none"' ?>>
        <div class="form-group">
            <?= $form->label('restrict_max_width', t('Maximum Width')) ?>
            <div class="input-group">
                <?= $form->number('restrict_max_width', $restrict_max_width > 0 ? $restrict_max_width : '', ['min' => 0, 'placeholder' => t('Empty for no limit')]) ?>
                <div class="input-group-addon"><?= t(/* i18n: short for pixels */ 'px') ?></div>
            </div>
        </div>
        <div class="form-group">
            <?= $form->label('restrict_max_height', t('Maximum Height')) ?>
            <div class="input-group">
                <?= $form->number('restrict_max_height', $restrict_max_height > 0 ? $restrict_max_height : '', ['min' => 0, 'placeholder' => t('Empty for no limit')]) ?>
                <div class="input-group-addon"><?= t(/* i18n: short for pixels */ 'px') ?></div>
            </div>
        </div>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button class="pull-right btn btn-primary" type="submit"><?= t('Save') ?></button>
        </div>
    </div>
</form>

<script>
$(document).ready(function() {
    var $div = $('#resizing-values');
    $('#restrict_uploaded_image_sizes')
        .on('change', function() {
            if ($(this).is(':checked')) {
                $div.show();
            } else {
                $div.hide();
            }
        })
        .trigger('change')
    ;
})();
</script>

