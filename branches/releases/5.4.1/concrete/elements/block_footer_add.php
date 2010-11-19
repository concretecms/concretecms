<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>

</div>

	<?php  if (is_array($extraParams)) { // defined within the area/content classes 
		foreach($extraParams as $key => $value) { ?>
			<input type="hidden" name="<?php echo $key?>" value="<?php echo $value?>">
		<?php  } ?>
	<?php  } ?>
	
	<?php  if (!$disableSubmit) { ?>
		<input type="hidden" name="_add" value="1">
	<?php  } ?>

	<div class="ccm-buttons">
	<a href="javascript:void(0)" <?php  if ($replaceOnUnload) { ?> onclick="location.href='<?php echo DIR_REL?>/<?php echo DISPATCHER_FILENAME?>?cID=<?php echo $c->getCollectionID()?>'; return true" class="ccm-button-left cancel" <?php  } else { ?> onclick="ccm_blockWindowClose()" class="ccm-button-left cancel"<?php  } ?>><span><em class="ccm-button-close"><?php echo t('Cancel')?></em></span></a>
	<a href="javascript:$('#ccm-form-submit-button').get(0).click()" class="ccm-button-right accept"><span><em class="ccm-button-add"><?php echo t('Add')?></em></span></a>
	
	<!-- we do it this way so we still trip javascript validation. stupid javascript. //-->
	
	<input type="submit" name="ccm-add-block-submit" value="submit" style="display: none" id="ccm-form-submit-button" />
	
	</div>

	<div class="ccm-spacer">&nbsp;</div>
	<input type="hidden" name="processBlock" value="1">
</form>