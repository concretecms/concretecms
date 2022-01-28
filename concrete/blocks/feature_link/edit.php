<?php
defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Editor\LinkAbstractor;
use Concrete\Core\Form\Service\DestinationPicker\DestinationPicker;

/** @var \Concrete\Core\Form\Service\Form $form */
/** @var \Concrete\Core\Editor\EditorInterface $editor */
/** @var \Concrete\Block\FeatureLink\Controller $controller */

$bID = $bID ?? 0;
$icon = $icon ?? '';

?>

<fieldset class="mb-3">
    <legend><?=t('Text')?></legend>
    <div class="mb-3">
        <?php echo $form->label("title", t('Title')); ?>
        <div class="input-group">
            <?php echo $form->text('title', $title ?? null); ?>
            <?php echo $form->select('titleFormat', \Concrete\Core\Block\BlockController::$btTitleFormats, $titleFormat, array('style' => 'width:105px;flex-grow:0;', 'class' => 'form-select')); ?>
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
    <legend><?=t('Button')?></legend>
    <div class="mb-3">
        <label class="form-label" for="buttonText"><?=t('Button Text')?></label>
        <input type="text" name="buttonText" class="form-control" value="<?=$buttonText ?? null?>">
    </div>
    <div class="form-group">
        <?php echo $form->label("buttonSize", t("Button Size")); ?>
        <?php echo $form->select("buttonSize", [
                '' => t('Regular'),
                'lg' => t('Large'),
                'sm' => t('Small')
            ], $buttonSize ?? null);
        ?>
    </div>
    <div class="form-group">
        <?php echo $form->label("buttonStyle", t("Button Style")); ?>
        <?php echo $form->select("buttonStyle", [
            '' => t('Regular'),
            'outline' => t('Outline'),
            'link' => t('Link'),
        ], $buttonStyle ?? null);
        ?>
    </div>
    <?php if ($themeColorCollection) { ?>
        <label class="form-label" for="buttonColor"><?=t('Button Color')?></label>
        <div data-vue="feature-link">
            <concrete-theme-color-input
                :color-collection='<?=json_encode($themeColorCollection)?>'
                <?php if (isset($buttonColor)) { ?> color="<?=$buttonColor?>"<?php } ?>
                input-name="buttonColor">
            </concrete-theme-color-input>
        </div>
    <?php } ?>

    <div class="form-group ccm-block-select-icon">
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
                el: 'div[data-vue=feature-link]',
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
