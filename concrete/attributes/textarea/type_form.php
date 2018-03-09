<fieldset>
<legend><?php echo t('Text Area Options'); ?></legend>

<div class="form-group" data-group="single-value">
    <label><?=t("Required"); ?></label>
    <div class="checkbox">
        <label>
            <?=$form->checkbox('akIsRequired', 1, $akIsRequired); ?> <span><?=t('Ensure a value is inputted.'); ?></span>
        </label>
    </div>
</div>

<div class="form-group">
	<?php echo $form->label('akTextareaDisplayMode', t('Input Format')); ?>
	<?php
    $akTextareaDisplayModeOptions = [
        'text' => t('Plain Text'),
        'rich_text' => t('Rich Text - Default Setting'),
    ];

    ?>
	<?php echo $form->select('akTextareaDisplayMode', $akTextareaDisplayModeOptions, $akTextareaDisplayMode, [
        'class' => 'span8',
    ]);?>
</div>

</fieldset>