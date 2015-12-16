<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<form action="<?=$view->action('save_control')?>" method="post">

    <?php
        $options = new \Concrete\Controller\Element\Dashboard\Express\Control\Options($control);
        if ($options->elementExists()) {
            print $options->render();
        }
    ?>

    <div class="form-group">
        <?=$form->label('customLabel', t('Custom Label'))?>
        <?=$form->text('customLabel', $control->getCustomLabel())?>
    </div>

    <? if ($type->supportsValidation()) { ?>
        <div class="form-group">
            <?=$form->label('isRequired', t('Required'))?>
            <div class="checkbox">
                <label><?=$form->checkbox('isRequired', 1, $control->isRequired())?> <?=t('Yes, require this form element')?></label>
            </div>
        </div>
    <? } ?>
    <?=Loader::helper('validation/token')->output('save_control')?>
</form>
<div class="dialog-buttons">
    <button class="btn btn-default" onclick="jQuery.fn.dialog.closeTop()"><?=t('Cancel')?></button>
    <button class="btn btn-primary pull-right"><?=t('Save')?></button>
</div>
