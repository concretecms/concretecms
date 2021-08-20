<?php
defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Editor\LinkAbstractor;
use Concrete\Core\Form\Service\DestinationPicker\DestinationPicker;

/** @var \Concrete\Core\Form\Service\Form $form */
/** @var \Concrete\Core\Application\Service\FileManager $fileManager */
/** @var \Concrete\Core\Editor\EditorInterface $editor */

?>

<fieldset class="mb-3">
    <legend><?=t('Basics')?></legend>
    <div class="mb-3">
        <label class="form-label" for="image"><?=t('Background Image')?></label>
        <?php echo $fileManager->image('image', 'image', t('Choose Image'), $image); ?>
    </div>
    <div class="mb-3">
        <label class="form-label" for="image"><?=t('Height')?></label>
        <input class="form-range" type="range" name="height" id="heroImageHeight" min="20" max="100" onchange="updateHeroImageHeight(this.value)" value="<?=$height?>">
        <div class="alert alert-info">
            <?=t('Current Value:')?> <code><span data-value="height"></span></code>
        </div>
    </div>
</fieldset>

<fieldset class="mb-3">
    <legend><?=t('Text')?></legend>
    <div class="mb-3">
        <label class="form-label" for="title"><?=t('Title')?></label>
        <input type="text" name="title" class="form-control" value="<?=$title?>">
    </div>
    <div class="mb-3">
        <label class="form-label" for="body"><?=t('Body')?></label>
        <?php
        echo $editor->outputBlockEditModeEditor('body', LinkAbstractor::translateFromEditMode($body));
        ?>
    </div>
</fieldset>

<fieldset class="mb-3">
    <legend><?=t('Button')?></legend>
    <div class="mb-3">
        <label class="form-label" for="buttonText"><?=t('Button Text')?></label>
        <input type="text" name="buttonText" class="form-control" value="<?=$buttonText?>">
        <div class="help-block">
            <?=t('Leave blank to omit the button.')?>
        </div>
    </div>
    <div class="form-group">
        <?php echo $form->label("buttonSize", t("Button Size")); ?>
        <?php echo $form->select("buttonSize", [
                '' => t('Regular'),
                'lg' => t('Large'),
                'sm' => t('Small')
            ], $buttonSize);
        ?>
    </div>
    <div class="form-group">
        <?php echo $form->label("buttonStyle", t("Button Style")); ?>
        <?php echo $form->select("buttonStyle", [
            '' => t('Regular'),
            'outline' => t('Outline'),
        ], $buttonStyle);
        ?>
    </div>
    <?php if ($themeColorCollection) { ?>
        <label class="form-label" for="buttonColor"><?=t('Button Color')?></label>
        <div data-vue="hero-image">
            <concrete-theme-color-input
                :color-collection='<?=json_encode($themeColorCollection)?>'
                <?php if ($buttonColor) { ?> color="<?=$buttonColor?>"<?php } ?>
                input-name="buttonColor">
            </concrete-theme-color-input>
        </div>
    <?php } ?>
    <div class="mb-3">
        <?php echo $form->label('buttonLink', t('Button Link')) ?>
        <?php echo $destinationPicker->generate(
            'imageLink',
            $imageLinkPickers,
            $imageLinkHandle,
            $imageLinkValue
        )
        ?>
    </div>
</fieldset>

<script type="text/javascript">
    $(function() {
        updateHeroImageHeight = function (value) {
            document.querySelector('span[data-value=height]').innerHTML = value
        }
        updateHeroImageHeight(document.getElementById('heroImageHeight').value)

        Concrete.Vue.activateContext('cms', function (Vue, config) {
            new Vue({
                el: 'div[data-vue=hero-image]',
                components: config.components
            })
        })

    })
</script>
