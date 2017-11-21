<?php
defined('C5_EXECUTE') or die('Access Denied.');

// Arguments
/* @var Concrete\Core\Page\View\PageView $this */
/* @var Concrete\Core\Html\Service\Html $html */
/* @var Concrete\Core\Application\Service\UserInterface $interface */
/* @var Concrete\Core\Application\Service\Dashboard $dashboard */
/* @var bool $hideDashboardPanel */
/* @var bool $_bookmarked */
/* @var string $pageTitle */
/* @var Concrete\Core\Validation\CSRF\Token $token */
/* @var Concrete\Core\Error\ErrorList\ErrorList $error */
/* @var Concrete\Core\Form\Service\Form $form */
/* @var Concrete\Core\Page\View\PageView $view */
/* @var Concrete\Controller\SinglePage\Dashboard\System\Files\Thumbnails\Options $controller */
/* @var Concrete\Core\Page\Page $c */
/* @var Concrete\Theme\Dashboard\PageTheme $theme */

/* @var array $thumbnail_generation_strategies */
/* @var string $thumbnail_generation_strategy */
/* @var array $thumbnail_formats */
/* @var string $thumbnail_format */
/* @var int $jpeg_compression */
/* @var int $png_compression */
/* @var array $manipulation_libraries */
/* @var string $manipulation_library */

?>
<form method="post" action="<?=$view->action('submit')?>">

    <?php $token->output('thumbnails-options') ?>

    <div class="form-group">
        <?= $form->label('thumbnail_generation_strategy', t('Thumbnail Generation Strategy')) ?>
        <?php
        foreach ($thumbnail_generation_strategies as $id => $name) {
            ?>
            <div class="radio">
                <label>
                    <?= $form->radio('thumbnail_generation_strategy', $id, $id === $thumbnail_generation_strategy, ['required' => 'required']) ?>
                    <?= h($name) ?>
                </label>
            </div>
            <?php
        }
        ?>
    </div>

    <div class="form-group">
        <?= $form->label('thumbnail_format', t('Thumbnails Format')) ?>
        <?php
        foreach ($thumbnail_formats as $id => $name) {
            ?>
            <div class="radio">
                <label>
                    <?= $form->radio('thumbnail_format', $id, $id === $thumbnail_format, ['required' => 'required']) ?>
                    <?= h($name) ?>
                </label>
            </div>
            <?php
        }
        ?>
    </div>

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
        <?= $form->label('create_high_dpi_thumbnails', t('Create high DPI thumbnails')) ?>
        <div class="checkbox">
            <label>
                <?= $form->checkbox('create_high_dpi_thumbnails', 1, $create_high_dpi_thumbnails) ?>
                <?= t('Create high DPI thumbnails') ?>
            </label>
        </div>
    </div>

	<div class="form-group">
		<?= $form->label('jpeg_compression', t('JPEG compression level'), ['class' => 'launch-tooltip control-label', 'title' => t('JPEG compression level ranges from 0 (worst quality, smaller file) to 100 (best quality, biggest file)')]) ?>
    	<?= $form->number('jpeg_compression', $jpeg_compression, ['required' => 'required', 'min' => '0', 'max' => '100']) ?>
	</div>

    <div class="form-group">
        <?= $form->label('png_compression', t('PNG compression quality'), ['class' => 'launch-tooltip control-label', 'title' => t('PNG compression quality ranges from 0 (no compression) to 9 (maximum compression)')]) ?>
        <?= $form->number('png_compression', $png_compression, ['required' => 'required', 'min' => '0', 'max' => '9']) ?>
    </div>


    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <a href="<?=URL::to('/dashboard/system/files/thumbnails')?>" class="btn btn-default"><?=t('Cancel')?></a>
            <button class="pull-right btn btn-primary" type="submit"><?=t('Save')?></button>
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
