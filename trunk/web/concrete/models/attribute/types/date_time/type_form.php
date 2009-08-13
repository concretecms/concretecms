<table class="entry-form" cellspacing="1" cellpadding="0">
<tr>
	<td class="subheader"><?=t('Ask User For')?></td>
</tr>
<tr>
	<? 
	$akDateDisplayModeOptions = array(
		'date_time' => t('Both Date and Time'),
		'date' => t('Date Only'),
		'text' => t('Text Input Field')

	);
	?>
	<td><?=$form->select('akDateDisplayMode', $akDateDisplayModeOptions, $akDateDisplayMode)?></td>
</tr>
</table>