<?php 
defined('C5_EXECUTE') or die("Access Denied.");
$step = ($_REQUEST['step']) ? "&step={$_REQUEST['step']}" : ""; 
$closeWindowCID=(intval($rcID))?intval($rcID):$c->getCollectionID();
?>

<?php  global $c; ?>
	
	<?php  if (is_array($extraParams)) { // defined within the area/content classes 
		foreach($extraParams as $key => $value) { ?>
			<input type="hidden" name="<?php echo $key?>" value="<?php echo $value?>">
		<?php  } ?>
	<?php  } ?>
	
	<div class="ccm-buttons">
	<a href="javascript:void(0)" <?php  if ($replaceOnUnload) { ?>onclick="location.href='<?php echo DIR_REL?>/<?php echo DISPATCHER_FILENAME?>?cID=<?php echo $closeWindowCID ?><?php echo $step?>'; return true" class="ccm-button-left cancel"<?php  } else { ?>class="ccm-button-left cancel" onclick="ccm_blockWindowClose()" <?php  } ?>><span><em class="ccm-button-close"><?php echo t('Cancel')?></em></span></a>
	<a href="javascript:clickedButton = true;$('#ccm-form-submit-button').get(0).click()" class="ccm-button-right accept"><span><em class="ccm-button-update"><?php echo t('Update')?></em></span></a>
	</div>	
	<div class="ccm-spacer">&nbsp;</div>

	<input type="hidden" name="update" value="1" />
	<input type="hidden" name="rarHandle" value="<?php echo $rarHandle?>" />
	<input type="hidden" name="rcID" value="<?php echo $rcID?>" />
	<input type="submit" name="ccm-edit-block-submit" value="submit" style="display: none" id="ccm-form-submit-button" />
	<input type="hidden" name="processBlock" value="1">

	</form>

</div>