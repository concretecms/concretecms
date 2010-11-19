<?php 
defined('C5_EXECUTE') or die("Access Denied.");
if (ENABLE_AREA_LAYOUTS == false) {
	die(t('Area layouts have been disabled.'));
}
global $c;
?>
 

<div>

	<input id="ccm_delete_layout_cvalID" name="ccm_delete_layout_cvalID" type="hidden" value="1" />

	<?php  if($_REQUEST['hasBlocks']){ ?>

		<div style="margin-bottom:16px;"><?php echo t("What would you like to do with this layout's blocks?") ?></div> 
		
		
		<input name="ccm_delete_layout_mode" type="radio" value="0" checked="checked" /> <?php echo t("Move them to the parent area below")?><br /><br />
		
		<input name="ccm_delete_layout_mode" type="radio" value="1" /> <?php echo t("Delete them")?><br /><br />
		
		
		<div class="ccm-buttons">
			<a href="#" class="ccm-button-left cancel" onclick="jQuery.fn.dialog.closeTop()"><span><em class="ccm-button-close"><?php echo t('Cancel')?></em></span></a>
			
			<a href="javascript:void(0)" onclick="deleteLayoutObj.deleteLayout($('input[name=ccm_delete_layout_mode]:checked').val())" class="ccm-button-right accept"><span><?php echo t('Remove Layout') ?></span></a>
		</div>	 
	
	<?php  }else{ ?>

		<div style="margin:8px 0px 16px 0px;"><?php echo t("Are you sure you want to delete this layout section?") ?></div>
		
		<div class="ccm-buttons">
			<a href="#" class="ccm-button-left cancel" onclick="jQuery.fn.dialog.closeTop()"><span><em class="ccm-button-close"><?php echo t('Cancel')?></em></span></a>
			
			<a href="javascript:void(0)" onclick="deleteLayoutObj.deleteLayout(1)" class="ccm-button-right accept"><span><?php echo t('Remove Layout') ?></span></a>
		</div>	

	<?php  } ?>

</div> 