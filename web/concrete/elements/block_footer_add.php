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

<? if (!$bt->supportsInlineAdd()) { ?>	

	<div class="ccm-buttons dialog-buttons">
	<a href="javascript:void(0)" onclick="jQuery.fn.dialog.closeTop()" class="btn btn-hover-danger btn-default pull-left"><?=t('Cancel')?></a>
	<a href="javascript:void(0)" onclick="$('#ccm-form-submit-button').get(0).click()" class="pull-right btn btn-primary"><?=t('Add')?></a>
	</div>

<? } ?>

	<!-- we do it this way so we still trip javascript validation. stupid javascript. //-->
	
	<input type="submit" name="ccm-add-block-submit" value="submit" style="display: none" id="ccm-form-submit-button" />

	<input type="hidden" name="processBlock" value="1">
</form>

</div>
