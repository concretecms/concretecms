<?
defined('C5_EXECUTE') or die("Access Denied.");
?>

</div>

<?
$bt = $b->getBlockTypeObject();

if (is_array($extraParams)) { // defined within the area/content classes 
	foreach($extraParams as $key => $value) { ?>
		<input type="hidden" name="<?=$key?>" value="<?=$value?>">
	<? } ?>
<? } ?>

<? if (!$b->getProxyBlock() && !$bt->supportsInlineEdit()) { ?>	
	<div class="ccm-buttons dialog-buttons">
	<a href="javascript:clickedButton = true;$('#ccm-form-submit-button').get(0).click()" class="btn pull-right btn-primary"><?=t('Save')?></a>
	<a style="float:left" href="javascript:void(0)" class="btn btn-default btn-hover-danger" onclick="jQuery.fn.dialog.closeTop()"><?=t('Cancel')?></a>
	</div>
<? } ?>

	<input type="submit" name="ccm-edit-block-submit" value="submit" style="display: none" id="ccm-form-submit-button" />

	</form>


<? 
$cont = $bt->getController();
if ($b->getBlockTypeHandle() == BLOCK_HANDLE_SCRAPBOOK_PROXY) {
	$bx = Block::getByID($b->getController()->getOriginalBlockID());
	$cont = $bx->getController();
}
?>

</div>