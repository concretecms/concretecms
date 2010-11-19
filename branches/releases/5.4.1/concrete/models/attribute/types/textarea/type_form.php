<table class="entry-form" cellspacing="1" cellpadding="0">
<tr>
	<td class="subheader"><?php echo t('Input Format')?></td>
</tr>
<tr>
	<?php  
	$akTextareaDisplayModeOptions = array(
		'text' => t('Plain Text'),
		'rich_text' => t('Rich Text - Simple (Default Setting)'),
		'rich_text_basic' => t('Rich Text - Basic Controls'),
		'rich_text_advanced' => t('Rich Text - Advanced'),
		'rich_text_office' => t('Rich Text - Office'),
		'rich_text_custom' => t('Rich Text - Custom')
	);
	?>
	<td><?php echo $form->select('akTextareaDisplayMode', $akTextareaDisplayModeOptions, $akTextareaDisplayMode)?></td>
</tr>
</table>