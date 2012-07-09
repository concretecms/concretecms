<?
defined('C5_EXECUTE') or die("Access Denied.");
global $c;?>
<?
if ($b->getBlockTypeHandle() == BLOCK_HANDLE_SCRAPBOOK_PROXY) {
	$bi = $b->getInstance();
	$bx = Block::getByID($bi->getOriginalBlockID());
	$bt = BlockType::getByID($bx->getBlockTypeID());
} else {
	$bt = BlockType::getByID($b->getBlockTypeID());
}
$templates = $bt->getBlockTypeCustomTemplates();
$txt = Loader::helper('text');
?>
<div class="ccm-ui" style="padding-top:10px;">
<form method="post" id="ccmCustomTemplateForm" action="<?=$b->getBlockUpdateInformationAction()?>&amp;rcID=<?=intval($rcID) ?>" class="form-vertical">
	
	<? if (count($templates) == 0) { ?>

		<?=t('There are no custom templates available.')?>

	<? } else { ?>
		
    	<div class="control-group">
          <label for="bFilename" class="control-label"><?=t('Custom Template')?></label>
            <div class="controls">
                <select id="bFilename" name="bFilename" class="xlarge">
                    <option value="">(<?=t('None selected')?>)</option>
                    <? foreach($templates as $tpl) { ?>
                        <option value="<?=$tpl?>" <? if ($b->getBlockFilename() == $tpl) { ?> selected <? } ?>><?	
                            if (strpos($tpl, '.') !== false) {
                                print substr($txt->unhandle($tpl), 0, strrpos($tpl, '.'));
                            } else {
                                print $txt->unhandle($tpl);
                            }
                            ?></option>		
                    <? } ?>
                </select>
            </div>
				</div>

	<? } ?>

        <div class="control-group" style="padding-top:10px">
          <label for="bName" class="control-label"><?=t('Block Name')?></label>
            <div class="controls">
						<input type="text" id="bName" name="bName" class="bName" value="<?=$b->getBlockName() ?>" />
            </div>
        </div>

		<div class="ccm-buttons dialog-buttons">
			<a href="#" class="btn ccm-dialog-close ccm-button-left cancel"><?=t('Cancel')?></a>
			<a href="javascript:void(0)" onclick="$('#ccmCustomTemplateForm').submit()" class="ccm-button-right accept primary btn"><span><?=t('Save')?></span></a>
		</div>
		
<?
$valt = Loader::helper('validation/token');
$valt->output();
?>
</form>
</div>

<script type="text/javascript">
$(function() {
	$('#ccmCustomTemplateForm').each(function() {
		ccm_setupBlockForm($(this), '<?=$b->getBlockID()?>', 'edit');
	});
});
</script>
