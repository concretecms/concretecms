<? 
//Used on both page and file attributes

if (count($attribs) > 0) { 

	$ih = Loader::helper('concrete/interface');
	$valt = Loader::helper('validation/token');
	
	?>
	
	
	<div style="margin:0px; padding:0px; width:100%; height:auto" >	
	<table border="0" cellspacing="1" cellpadding="0" class="grid-list">
	<tr>
		<td class="subheader" width="100%"><?=t('Name')?></td>
		<td class="subheader"><?=t('Handle')?></td>
		<td class="subheader"><div style="width: 60px"></div></td>
		<td class="subheader"><div style="width: 70px"></div></td>
	</tr>
	<?
	foreach($attribs as $ak) { ?>
		<tr>
			<td><?=$ak->getAttributeKeyName()?></td>
			<td style="white-space: nowrap"><?=$ak->getAttributeKeyHandle()?></td>
			<td><? print $ih->button(t('Edit'), $this->url($editURL, 'edit', $ak->getAttributeKeyID()));?>
			</td>
			<td><? print $ih->button_js(t('Delete'), "javascript:if (confirm('".t('Are you sure you wish to delete this attribute?')."')) { location.href='" . $this->url($editURL, 'delete', $ak->getAttributeKeyID(), $valt->generate('delete_attribute')) . "' }")?></td>
		</tr>
	<? } ?>
	</table>
	</div>	

<? } else { ?>
	
	<br/>
	
	<strong>
		<?
	 echo t('No attributes defined.');
		?>
	</strong>
	
	<br/><br/>
	
<? } ?>