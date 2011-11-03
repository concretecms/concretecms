<fieldset>
<legend><?=t('Text Area Options')?></legend>

<div class="clearfix">
<?=$form->label('akTextareaDisplayMode', t('Input Format'))?>
<div class="input">
	<? 
	$akTextareaDisplayModeOptions = array(
		'text' => t('Plain Text'),
		'rich_text' => t('Rich Text - Simple (Default Setting)'),
		'rich_text_basic' => t('Rich Text - Basic Controls'),
		'rich_text_advanced' => t('Rich Text - Advanced'),
		'rich_text_office' => t('Rich Text - Office'),
		'rich_text_custom' => t('Rich Text - Custom')
	);
	?>
	<?=$form->select('akTextareaDisplayMode', $akTextareaDisplayModeOptions, $akTextareaDisplayMode)?>
</div>
</div>