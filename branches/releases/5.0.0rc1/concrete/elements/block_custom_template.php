<?php  global $c;?>
<?php 
$bt = BlockType::getByID($b->getBlockTypeID());
$templates = $bt->getBlockTypeCustomTemplates();
$txt = Loader::helper('text');
?>
<form method="post" id="ccmCustomTemplateForm" action="<?php echo $b->getBlockUpdateInformationAction()?>">
	
	<strong>Custom Template</strong>:<br>
	<?php  if (count($templates) == 0) { ?>
		There are no custom templates available.

	<div class="ccm-buttons">
	<a href="#" class="ccm-dialog-close ccm-button-left cancel"><span><em class="ccm-button-close">Cancel</em></span></a>
	</div>

	<?php  } else { ?>
	<select name="bFilename">
		<option value="">(None selected)</option>
		<?php  foreach($templates as $tpl) { ?>
			<option value="<?php echo $tpl?>" <?php  if ($b->getBlockFilename() == $tpl) { ?> selected <?php  } ?>><?php echo substr($txt->uncamelcase($tpl), 0, strrpos($tpl, '.'))?></option>		
		<?php  } ?>
	</select>
	<div class="ccm-buttons">
	<a href="#" class="ccm-dialog-close ccm-button-left cancel"><span><em class="ccm-button-close">Cancel</em></span></a>
	<a href="javascript:$('#ccmCustomTemplateForm').get(0).submit()" class="ccm-button-right accept"><span>Update</span></a>
	</div>
	<?php  } ?>
</form>