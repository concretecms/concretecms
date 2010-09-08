<? defined('C5_EXECUTE') or die("Access Denied."); ?>

</div>

	<? if (is_array($extraParams)) { // defined within the area/content classes 
		foreach($extraParams as $key => $value) { ?>
			<input type="hidden" name="<?=$key?>" value="<?=$value?>">
		<? } ?>
	<? } ?>
	
	<? if (!$disableSubmit) { ?>
		<input type="hidden" name="_add" value="1">
	<? } ?>

	<div class="ccm-buttons">
	<a href="javascript:void(0)" <? if ($replaceOnUnload) { ?> onclick="location.href='<?=DIR_REL?>/<?=DISPATCHER_FILENAME?>?cID=<?=$c->getCollectionID()?>'; return true" class="ccm-button-left cancel" <? } else { ?> onclick="ccm_blockWindowClose()" class="ccm-button-left cancel"<? } ?>><span><em class="ccm-button-close"><?=t('Cancel')?></em></span></a>
	<a href="javascript:$('#ccm-form-submit-button').get(0).click()" class="ccm-button-right accept"><span><em class="ccm-button-add"><?=t('Add')?></em></span></a>
	
	<!-- we do it this way so we still trip javascript validation. stupid javascript. //-->
	
	<input type="submit" name="ccm-add-block-submit" value="submit" style="display: none" id="ccm-form-submit-button" />
	
	</div>

	<div class="ccm-spacer">&nbsp;</div>
	<input type="hidden" name="processBlock" value="1">
</form>