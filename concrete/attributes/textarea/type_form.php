<fieldset>
<legend><?php echo t('Text Area Options')?></legend>

<div class="form-group">
	<?php echo $form->label('akTextareaDisplayMode', t('Input Format'))?>
	<?php
    $akTextareaDisplayModeOptions = [
        'text' => t('Plain Text'),
        'rich_text' => t('Rich Text - Default Setting'),
    ];

    ?>
	<?php echo $form->select('akTextareaDisplayMode', $akTextareaDisplayModeOptions, $akTextareaDisplayMode ?? 'text')?>
</div>

</fieldset>
