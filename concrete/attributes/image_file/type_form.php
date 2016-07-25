<fieldset>
<legend><?php echo t('Text Area Options')?></legend>

<div class="form-group">
	<?php echo $form->label('mode', t('Input Format'))?>
	<?php
    $options = array(
        \Concrete\Core\Entity\Attribute\Key\Type\ImageFileType::TYPE_FILE_MANAGER => t('File Manager Selector'),
        \Concrete\Core\Entity\Attribute\Key\Type\ImageFileType::TYPE_HTML_INPUT => t('HTML Input'),
    );

    ?>
	<?php echo $form->select('mode', $options, $mode)?>
</div>
</fieldset>