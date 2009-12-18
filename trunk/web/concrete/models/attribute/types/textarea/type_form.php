<table class="entry-form" cellspacing="1" cellpadding="0">
<tr>
	<td class="subheader"><?=t('Input Format')?></td>
</tr>
<tr>
	<? 
	$akTextareaDisplayModeOptions = array(
		'text' => t('Plain Text'),
		'rich_text' => t('Rich Text')
	);
	?>
	<td><?=$form->select('akTextareaDisplayMode', $akTextareaDisplayModeOptions, $akTextareaDisplayMode)?></td>
</tr>
</table>