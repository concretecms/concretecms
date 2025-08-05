<?php

use Concrete\Core\Application\Service\FileManager;
use Concrete\Core\Support\Facade\Application;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var \Concrete\Block\Feature\Controller $controller
 * @var \Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\Form\Service\DestinationPicker\DestinationPicker $destinationPicker
 * @var array $linkDestinationPickers
 * @var string $linkDestinationHandle
 * @var mixed $linkDestinationValue
 * @var string $sizingOption
 * @var array $themeResponsiveImageMap
 * @var array $selectedThumbnailTypes
 * @var array $imageLinkPickers
 * @var string $imageLinkHandle
 * @var mixed $imageLinkValue
 * @var int $constrainImage
 * @var Concrete\Core\Entity\File\File|null $bf
 */

$bID = $bID ?? 0;
$icon = $icon ?? '';
$title = $title ?? '';
$titleFormat = $titleFormat ?? '';

$app = Application::getFacadeApplication();
$fileManager = $app->make(FileManager::class);

?>
<fieldset>
    <legend><?=t('Display')?></legend>
    <div class="form-group ccm-block-select-icon">
        <?php echo $form->label('icon', t('Icon'))?>
        <div id="ccm-icon-selector-<?= h($bID) ?>">
            <icon-selector name="icon" selected="<?= h($icon) ?>" title="<?= t('Choose Icon') ?>" empty-option-label="<?= h(tc('Icon', '** None Selected')) ?>" />
        </div>
    </div>

    <div class="form-group">
        <?php
        echo $form->label('ccm-b-image', t('Image'));
        echo $fileManager->image('ccm-b-image', 'fID', t('Choose Image'), $bf);
        ?>
        <p class="text-muted small">If Image is set, no icon will appear</p>
    </div>

    <div class="form-group">
        <?php echo $form->label("title", t('Title')); ?>
	    <div class="input-group">
		    <?php echo $form->text('title', $title); ?>
			<?php echo $form->select('titleFormat', \Concrete\Core\Block\BlockController::$btTitleFormats, $titleFormat, array('style' => 'width:105px;flex-grow:0;', 'class' => 'form-select')); ?>
		</div>
	</div>

    <div class="form-group">
        <?php echo $form->label('paragraph', t('Paragraph:'));?>
        <?php
            $editor = Core::make('editor');
            echo $editor->outputBlockEditModeEditor('paragraph', htmlspecialchars($controller->getParagraphEditMode(), ENT_QUOTES, APP_CHARSET));
        ?>
    </div>

</fieldset>

<fieldset>
    <legend><?=t('Link')?></legend>
    <?= $destinationPicker->generate('link', $linkDestinationPickers, $linkDestinationHandle, $linkDestinationValue) ?>
</fieldset>

<script type="text/javascript">
$(function() {
    Concrete.Vue.activateContext('cms', function(Vue, config) {
        new Vue({
            el: '#ccm-icon-selector-<?= h($bID) ?>',
            components: config.components
        })
    })
});
</script>

<style type="text/css">
    div.ccm-block-select-icon .input-group-addon {
        min-width:70px;
    }
    div.ccm-block-select-icon i {
        font-size: 22px;
    }
</style>
