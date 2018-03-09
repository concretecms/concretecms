<fieldset>
<legend><?php echo t('Image/File Options'); ?></legend>

<div class="form-group" data-group="single-value">
    <label><?=t("Required"); ?></label>
    <div class="checkbox">
        <label>
            <?=$form->checkbox('akIsRequired', 1, $akIsRequired); ?> <span><?=t('Ensure a value is selected.'); ?></span>
        </label>
    </div>
</div>

<div class="form-group">
	<?php echo $form->label('mode', t('Input Format')); ?>
	<?php
    $options = [
        \Concrete\Core\Entity\Attribute\Key\Settings\ImageFileSettings::TYPE_FILE_MANAGER => t('File Manager Selector'),
        \Concrete\Core\Entity\Attribute\Key\Settings\ImageFileSettings::TYPE_HTML_INPUT => t('HTML Input'),
    ];

    ?>
	<?php echo $form->select('mode', $options, $mode);?>
</div>
</fieldset>