<?php
defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Editor\LinkAbstractor;
use Concrete\Core\Form\Service\DestinationPicker\DestinationPicker;

/** @var \Concrete\Core\Form\Service\Form $form */
/** @var \Concrete\Core\Editor\EditorInterface $editor */
/** @var \Concrete\Block\FeatureLink\Controller $controller */

$bID = $bID ?? 0;
$icon = $icon ?? '';

use Concrete\Core\Application\Service\FileManager;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Form\Service\Widget\PageSelector;
use Concrete\Core\Support\Facade\Application;

/**
 * @var DestinationPicker $destinationPicker
 * @var string $sizingOption
 * @var array $themeResponsiveImageMap
 * @var array $thumbnailTypes
 * @var array $selectedThumbnailTypes
 * @var array $imageLinkPickers
 * @var string $imageLinkHandle
 * @var mixed $imageLinkValue
 * @var int $constrainImage
 * @var File|null $bfo
 */

 $app = Application::getFacadeApplication();
/** @var PageSelector $pageSelector */
$pageSelector = $app->make(PageSelector::class);
/** @var FileManager $fileManager */
$fileManager = $app->make(FileManager::class);

$thumbnailTypes['0'] = t('Full Size');


?>

<fieldset class="mb-3">
    <legend><?=t('Text')?></legend>
    <div class="mb-3">
        <?php echo $form->label("title", t('Title')); ?>
        <div class="input-group">
            <?php echo $form->text('title', $title ?? null); ?>
            <?php echo $form->select('titleFormat', \Concrete\Core\Block\BlockController::$btTitleFormats, $titleFormat ?? null, array('style' => 'width:105px;flex-grow:0;', 'class' => 'form-select')); ?>
        </div>
    </div>
    <div class="mb-3">
        <label class="form-label" for="body"><?=t('Body')?></label>
        <?php
        echo $editor->outputBlockEditModeEditor('body', isset($body) ? LinkAbstractor::translateFromEditMode($body) : null);
        ?>
    </div>
</fieldset>

<fieldset class="mb-3">
<legend><?=t('Image')?></legend>
    <div class="form-group">
        <?php
        echo $fileManager->image('ccm-b-image', 'fID', t('Choose Image'), $bf);
        ?>
    </div>
</fieldset>

<fieldset class="mb-3">
    <legend><?=t('Button')?></legend>
    <div class="mb-3">
        <label class="form-label" for="buttonText"><?=t('Button Text')?></label>
        <input type="text" name="buttonText" class="form-control" value="<?=$buttonText ?? null?>">
    </div>
    <div class="mb-3">
        <?php echo $form->label("buttonSize", t("Button Size")); ?>
        <?php echo $form->select("buttonSize", [
                '' => t('Regular'),
                'lg' => t('Large'),
                'sm' => t('Small')
            ], $buttonSize ?? null);
        ?>
    </div>
    <div class="mb-3">
        <?php echo $form->label("buttonStyle", t("Button Style")); ?>
        <?php echo $form->select("buttonStyle", [
            '' => t('Regular'),
            'outline' => t('Outline'),
            'link' => t('Link'),
        ], $buttonStyle ?? null);
        ?>
    </div>
    <?php if ($themeColorCollection) { ?>
        <div class="mb-3">
            <label class="form-label" for="buttonColor"><?=t('Button Color')?></label>
            <div data-vue-app="feature-link">
                <concrete-theme-color-input
                    :color-collection='<?=json_encode($themeColorCollection)?>'
                    <?php if (isset($buttonColor)) { ?> color="<?=$buttonColor?>"<?php } ?>
                    input-name="buttonColor">
                </concrete-theme-color-input>
            </div>
        </div>
    <?php } ?>

    <div class="mb-3 ccm-block-select-icon">
        <?php echo $form->label('icon', t('Icon'))?>
        <div id="ccm-icon-selector-<?= h($bID) ?>">
            <icon-selector name="icon" selected="<?= h($icon) ?>" title="<?= t('Choose Icon') ?>" empty-option-label="<?= h(tc('Icon', '** None Selected')) ?>" />
        </div>
    </div>

    <div class="mb-3">
        <?php echo $form->label('buttonLink', t('Button Link')) ?>
        <?php echo $destinationPicker->generate(
            'imageLink',
            $imageLinkPickers,
            $imageLinkHandle,
            $imageLinkValue
        )
        ?>
        <div class="help-block">
            <?=t('Set to None to omit the button.')?>
        </div>
    </div>
</fieldset>

<script type="text/javascript">
    $(function() {

        Concrete.Vue.activateContext('cms', function (Vue, config) {
            new Vue({
                el: 'div[data-vue-app=feature-link]',
                components: config.components
            })
        })

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
