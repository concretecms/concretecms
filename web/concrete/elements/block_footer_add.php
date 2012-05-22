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

	<div class="ccm-buttons dialog-buttons">
	<a href="javascript:void(0)" <? if ($replaceOnUnload) { ?> onclick="location.href='<?=DIR_REL?>/<?=DISPATCHER_FILENAME?>?cID=<?=$c->getCollectionID()?>'; return true" class="btn ccm-button-left cancel" <? } else { ?> onclick="ccm_blockWindowClose()" class="btn ccm-button-left cancel"<? } ?>><?=t('Cancel')?></a>
	<a href="javascript:void(0)" onclick="$('#ccm-form-submit-button').get(0).click()" class="ccm-button-right accept btn primary"><?=t('Add')?> <i class="icon-plus-sign icon-white"></i></a>
	</div>
	
	<!-- we do it this way so we still trip javascript validation. stupid javascript. //-->
	
	<input type="submit" name="ccm-add-block-submit" value="submit" style="display: none" id="ccm-form-submit-button" />

	<input type="hidden" name="processBlock" value="1">
</form>

<? 
$cont = $bt->getController();
if ($cont->getBlockTypeWrapperClass() != '') { ?>
</div>
<? } ?>
