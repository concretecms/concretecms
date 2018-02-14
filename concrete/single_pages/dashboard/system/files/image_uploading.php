<?php
defined('C5_EXECUTE') or die('Access Denied.');

/* @var Concrete\Core\Page\View\PageView $view */
/* @var Concrete\Core\Validation\CSRF\Token $token */
/* @var Concrete\Core\Form\Service\Form $form */

/* @var array $manipulation_libraries */
/* @var string $manipulation_library */
/* @var int $jpeg_quality */
/* @var int $png_compression */
/* @var bool $use_exif_data_to_rotate_images */
/* @var int $restrict_max_width */
/* @var int $restrict_max_height */
/* @var bool $use_exif_data_to_rotate_images */
/* @var string|null $thumbnailOptionsURL */
?>
<form method="POST" action="<?= $view->action('save') ?>">
    <?= $token->output('image-options') ?>

    <div class="form-group">
        <?= $form->label('manipulation_library', t('Image Manipulation Library')) ?>
        <?php
        foreach ($manipulation_libraries as $id => $name) {
            ?>
            <div class="radio">
                <label>
                    <?= $form->radio('manipulation_library', $id, $id === $manipulation_library, ['required' => 'required']) ?>
                    <?= h($name) ?>
                    <?= t('(currently working: %s)', '<span class="ccm-check-manipulation-library" data-check-src="' . h($view->action('test_manipulation_library', $id, $token->generate('thumbnail-check-library-' . $id))) . '"><i class="fa fa-spinner fa-spin"></i></span>')?>
                </label>
            </div>
            <?php
        }
        ?>
    </div>

    <div class="form-group">
        <?= $form->label('jpeg_quality', t('JPEG quality'), ['class' => 'launch-tooltip control-label', 'title' => t('JPEG quality ranges from 0 (worst quality, smaller file) to 100 (best quality, biggest file)')]) ?>
        <?= $form->number('jpeg_quality', $jpeg_quality, ['required' => 'required', 'min' => '0', 'max' => '100']) ?>
    </div>

    <div class="form-group">
        <?= $form->label('png_compression', t('PNG compression quality'), ['class' => 'launch-tooltip control-label', 'title' => t('PNG compression quality ranges from 0 (no compression) to 9 (maximum compression)')]) ?>
        <?= $form->number('png_compression', $png_compression, ['required' => 'required', 'min' => '0', 'max' => '9']) ?>
    </div>

    <div class="form-group">
        <?= $form->label('restrict_max_width', t('Maximum width of uploaded images'), ['class' => 'launch-tooltip', 'title' => t('Here you can set the maximum width of uploaded images: images wider that this value will be scaled down. Leave empty to allow any image width.')]) ?>
        <div class="input-group">
            <?= $form->number('restrict_max_width', $restrict_max_width > 0 ? $restrict_max_width : '', ['min' => '0']) ?>
            <div class="input-group-addon"><?= t(/* i18n: short for pixels */ 'px') ?></div>
        </div>
    </div>
    <div class="form-group">
        <?= $form->label('restrict_max_height', t('Maximum height of uploaded images'), ['class' => 'launch-tooltip', 'title' => t('Here you can set the maximum height of uploaded images: images taller that this value will be scaled down. Leave empty to allow any image height.')]) ?>
        <div class="input-group">
            <?= $form->number('restrict_max_height', $restrict_max_height > 0 ? $restrict_max_height : '', ['min' => '0']) ?>
            <div class="input-group-addon"><?= t(/* i18n: short for pixels */ 'px') ?></div>
        </div>
    </div>

    <div class="form-group">
        <?= $form->label('', t('Other Options')) ?>
        <div class="checkbox">
            <label>
                <?= $form->checkbox('use_exif_data_to_rotate_images', 1, $use_exif_data_to_rotate_images) ?>
                <?= t('Use EXIF metadata to auto-rotate images uploaded images.') ?>
            </label>
        </div>
    </div>

    <?php
    if ($thumbnailOptionsURL !== null) {
        ?>
        <div class="alert alert-info">
            <?= t('Looking for thumbnail options? You can find them <a href="%s">here</a>.', $thumbnailOptionsURL) ?>
        </div>
        <?php
    }
    ?>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button class="pull-right btn btn-primary" type="submit"><?= t('Save') ?></button>
        </div>
    </div>
</form>
<script>
$(window).load(function() {
    function checked($container, ok) {
        $container.html(
            ok ? '<i class="fa fa-check" style="color: green"></i>' : '<i class="fa fa-remove" style="color: red"></i>'
        );
    }
    $('.ccm-check-manipulation-library').each(function() {
        var $container = $(this);
        $container.append($('<img style="visibility: hidden; width: 1px; height: 1px" />')
            .on('load', function() {
                checked($container, true);
            })
            .on('error', function() {
                checked($container, false);
            })
            .attr('src', $container.data('check-src'))
        );
    });
});
</script>
