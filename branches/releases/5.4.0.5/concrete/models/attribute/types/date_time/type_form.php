<table class="entry-form" cellspacing="1" cellpadding="0">
<tr>
	<td class="subheader"><?php echo t('Ask User For')?></td>
</tr>
<tr>
	<?php  
	$akDateDisplayModeOptions = array(
		'date_time' => t('Both Date and Time'),
		'date' => t('Date Only'),
		'text' => t('Text Input Field')

	);
	?>
	<td><?php echo $form->select('akDateDisplayMode', $akDateDisplayModeOptions, $akDateDisplayMode)?></td>
</tr>
</table>