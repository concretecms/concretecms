<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
global $c;?>
<?php 
$bt = BlockType::getByID($b->getBlockTypeID());
$templates = $bt->getBlockTypeCustomTemplates();
$txt = Loader::helper('text');
?>
<form method="post" id="ccmCustomTemplateForm" action="<?php echo $b->getBlockUpdateInformationAction()?>">
	
	<strong><?php echo t('Custom Template')?></strong>:<br>
	
	<?php  if (count($templates) == 0) { ?>
	
		<?php echo t('There are no custom templates available.')?>
		<div class="ccm-buttons">
		<a href="#" class="ccm-dialog-close ccm-button-left cancel"><span><em class="ccm-button-close"><?php echo t('Cancel')?></em></span></a>
		</div>

	<?php  } else { ?>
	
		<select name="bFilename">
			<option value="">(<?php echo t('None selected')?>)</option>
			<?php  foreach($templates as $tpl) { ?>
				<option value="<?php echo $tpl?>" <?php  if ($b->getBlockFilename() == $tpl) { ?> selected <?php  } ?>><?php 	
					if (strpos($tpl, '.') !== false) {
						print substr($txt->unhandle($tpl), 0, strrpos($tpl, '.'));
					} else {
						print $txt->unhandle($tpl);
					}
					?></option>		
			<?php  } ?>
		</select>
		<div class="ccm-buttons">
		<a href="#" class="ccm-dialog-close ccm-button-left cancel"><span><em class="ccm-button-close"><?php echo t('Cancel')?></em></span></a>
		<a href="javascript:$('#ccmCustomTemplateForm').get(0).submit()" class="ccm-button-right accept"><span><?php echo t('Update')?></span></a>
		</div>
		
	<?php  } ?>
<?php 
$valt = Loader::helper('validation/token');
$valt->output();
?>
</form>