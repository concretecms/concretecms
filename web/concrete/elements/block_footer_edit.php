<?
defined('C5_EXECUTE') or die("Access Denied.");
$step = ($_REQUEST['step']) ? "&step={$_REQUEST['step']}" : ""; 
$closeWindowCID=(intval($rcID))?intval($rcID):$c->getCollectionID();
?>

</div>

<?
$bt = $b->getBlockTypeObject();
if ($bt->supportsInlineEdit()) { ?>

<script type="text/javascript">
$(document).unbind('inlineEditCancel').on('inlineEditCancel', function(e, onComplete) {
	jQuery.fn.dialog.showLoader();
	var action = CCM_TOOLS_PATH + '/edit_block_popup?cID=<?=$c->getCollectionID()?>&bID=<?=$b->getBlockID()?>&arHandle=<?=htmlspecialchars($a->getAreaHandle())?>&btask=view_edit_mode';	 
	$.get(action, 		
		function(r) { 
			$('[data-block-id=<?=$b->getBlockID()?>][data-area-id=<?=$a->getAreaID()?>]').before(r).remove();
			if (onComplete) {
				onComplete();
			}
			CCMInlineEditMode.finishExit();
		}
	);
});
</script>

<? } ?>

<? global $c; ?>
	
	<? if (is_array($extraParams)) { // defined within the area/content classes 
		foreach($extraParams as $key => $value) { ?>
			<input type="hidden" name="<?=$key?>" value="<?=$value?>">
		<? } ?>
	<? } ?>

<? if (!$b->getProxyBlock() && !$bt->supportsInlineEdit()) { ?>	
	<div class="ccm-buttons dialog-buttons">
	<a href="javascript:clickedButton = true;$('#ccm-form-submit-button').get(0).click()" class="btn pull-right btn-primary"><?=t('Save')?></a>
	<a style="float:left" href="javascript:void(0)" <? if ($replaceOnUnload) { ?>onclick="location.href='<?=DIR_REL?>/<?=DISPATCHER_FILENAME?>?cID=<?=$closeWindowCID ?><?=$step?>'; return true" class="btn btn-hover-danger"<? } else { ?>class="btn btn-hover-danger" onclick="$(document).trigger('blockWindowClose');jQuery.fn.dialog.closeTop();" <? } ?>><?=t('Cancel')?></a>
	</div>
<? } ?>

	<input type="hidden" name="update" value="1" />
	<input type="hidden" name="rcID" value="<?=$rcID?>" />
	<input type="submit" name="ccm-edit-block-submit" value="submit" style="display: none" id="ccm-form-submit-button" />
	<input type="hidden" name="processBlock" value="1">

	</form>


<? 
$cont = $bt->getController();
if ($b->getBlockTypeHandle() == BLOCK_HANDLE_SCRAPBOOK_PROXY) {
	$bx = Block::getByID($b->getController()->getOriginalBlockID());
	$cont = $bx->getController();
}
?>

</div>

