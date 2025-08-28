<?php
/**
 * @var Concrete\Attribute\Textarea\Controller $controller
 * @var Concrete\Core\Attribute\View $view
 * @var Concrete\Core\Form\Service\Form $form
 * @var string $akTextareaDisplayMode
 */
?>
<fieldset>
<legend><?php echo t('Text Area Options')?></legend>

<div class="form-group">
	<?php echo $form->label('akTextareaDisplayMode', t('Input Format'))?>
	<?php
    $akTextareaDisplayModeOptions = [
        $controller::MODE_TEXT => t('Plain Text'),
        $controller::MODE_RICHTEXT => t('Rich Text - Default Setting'),
    ];

    ?>
	<?php echo $form->select('akTextareaDisplayMode', $akTextareaDisplayModeOptions, $akTextareaDisplayMode ?? $controller::MODE_DEFAULT)?>
</div>

</fieldset>
