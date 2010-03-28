<table class="entry-form" cellspacing="1" cellpadding="0">
<tr>
	<td class="subheader"><?php echo t('Input Format')?></td>
</tr>
<tr>
	<?php  
	$akTextareaDisplayModeOptions = array(
		'text' => t('Plain Text'),
		'rich_text' => t('Rich Text')
	);
	?>
	<td><?php echo $form->select('akTextareaDisplayMode', $akTextareaDisplayModeOptions, $akTextareaDisplayMode)?></td>
</tr>
</table>