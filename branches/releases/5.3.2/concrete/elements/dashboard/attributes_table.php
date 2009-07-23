<?php  
//Used on both page and file attributes

if (count($attribs) > 0) { 

	$ih = Loader::helper('concrete/interface');
	$valt = Loader::helper('validation/token');
	
	?>
	
	
	<div style="margin:0px; padding:0px; width:100%; height:auto" >	
	<table border="0" cellspacing="1" cellpadding="0" class="grid-list">
	<tr>
		<td class="subheader" width="100%"><?php echo t('Name')?></td>
		<td class="subheader"><?php echo t('Handle')?></td>
		<td class="subheader"><div style="width: 60px"></div></td>
		<td class="subheader"><div style="width: 70px"></div></td>
	</tr>
	<?php 
	foreach($attribs as $ak) { ?>
		<tr>
			<td><?php echo $ak->getAttributeKeyName()?></td>
			<td style="white-space: nowrap"><?php echo $ak->getAttributeKeyHandle()?></td>
			<td>
				<?php 
				if($attributeType=='file')
					 $target='/dashboard/files/attributes/-/edit/?fakID=' . $ak->getAttributeKeyID();
				else $target='/dashboard/pages/types/attributes?akID=' . $ak->getAttributeKeyID() . '&task=edit';
				print $ih->button(t('Edit'), $this->url($target) );
				?>
			</td>
			<td>
				<?php  
				if($attributeType=='file')
					 $target='/dashboard/files/attributes/-/delete/?fakID=' . $ak->getAttributeKeyID().'&' . $valt->getParameter('delete_attribute');
				else $target='/dashboard/pages/types/attributes?akID=' . $ak->getAttributeKeyID() . '&task=delete&' . $valt->getParameter('delete_attribute');
				print $ih->button(t('Delete'), "javascript:if (confirm('".t('Are you sure you wish to delete this attribute?')."')) { location.href='" . $this->url($target) . "' }");
				?>
			</td>
		</tr>
	<?php  } ?>
	</table>
	</div>	

<?php  } else { ?>
	
	<br/>
	
	<strong>
		<?php 
		if($attributeType=='file') echo t('No file attributes defined.');
		else echo t('No page attributes defined.');
		?>
	</strong>
	
	<br/><br/>
	
<?php  } ?>