<?php
defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Editor\LinkAbstractor;
use Concrete\Core\Form\Service\DestinationPicker\DestinationPicker;

/** @var \Concrete\Core\Form\Service\Form $form */
/** @var \Concrete\Core\Application\Service\FileManager $fileManager */
/** @var \Concrete\Core\Editor\EditorInterface $editor */

?>

<div data-view="edit-top-navigation-bar-block">
    <div class="mb-3">
        <label class="form-label" for="image"><?=t('Background Image')?></label>
        <?php echo $fileManager->image('image', 'image', t('Choose Image'), $image); ?>
    </div>
</div>

<fieldset class="mb-3">
    <div class="mb-3">
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
    </div>
</fieldset>

<fieldset class="mb-3">
    <div class="mb-3">
        <legend><?=t('Button')?></legend>
        <div class="mb-3">
            <label class="form-label" for="buttonText"><?=t('Button Text')?></label>
            <input type="text" name="buttonText" class="form-control" value="<?=$buttonText?>">
            <div class="help-block">
                <?=t('Leave blank to omit the button.')?>
            </div>
        </div>
        <div class="mb-3">
            <?php echo $form->label('buttonLink', t('Image Link')) ?>
            <?php echo $destinationPicker->generate(
                'imageLink',
                $imageLinkPickers,
                $imageLinkHandle,
                $imageLinkValue
            )
            ?>
        </div>
    </div>
</fieldset>

