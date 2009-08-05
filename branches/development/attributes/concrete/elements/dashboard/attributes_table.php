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
			<td>
				<? 
				if($attributeType=='file')
					 $target='/dashboard/files/attributes/-/delete/?fakID=' . $ak->getAttributeKeyID().'&' . $valt->getParameter('delete_attribute');
				else $target='/dashboard/pages/types/attributes?akID=' . $ak->getAttributeKeyID() . '&task=delete&' . $valt->getParameter('delete_attribute');
				print $ih->button(t('Delete'), "javascript:if (confirm('".t('Are you sure you wish to delete this attribute?')."')) { location.href='" . $this->url($target) . "' }");
				?>
			</td>
		</tr>
	<? } ?>
	</table>
	</div>	

<? } else { ?>
	
	<br/>
	
	<strong>
		<?
		if($attributeType=='file') echo t('No file attributes defined.');
		else echo t('No page attributes defined.');
		?>
	</strong>
	
	<br/><br/>
	
<? } ?>