<? global $c;?>
<?
$bt = BlockType::getByID($b->getBlockTypeID());
$templates = $bt->getBlockTypeCustomTemplates();
$txt = Loader::helper('text');
?>
<form method="post" id="ccmCustomTemplateForm" action="<?=$b->getBlockUpdateInformationAction()?>">
	
	<strong>Custom Template</strong>:<br>
	<? if (count($templates) == 0) { ?>
		There are no custom templates available.

	<div class="ccm-buttons">
	<a href="#" class="ccm-dialog-close ccm-button-left cancel"><span><em class="ccm-button-close">Cancel</em></span></a>
	</div>

	<? } else { ?>
	<select name="bFilename">
		<option value="">(None selected)</option>
		<? foreach($templates as $tpl) { ?>
			<option value="<?=$tpl?>" <? if ($b->getBlockFilename() == $tpl) { ?> selected <? } ?>><?=substr($txt->unhandle($tpl), 0, strrpos($tpl, '.'))?></option>		
		<? } ?>
	</select>
	<div class="ccm-buttons">
	<a href="#" class="ccm-dialog-close ccm-button-left cancel"><span><em class="ccm-button-close">Cancel</em></span></a>
	<a href="javascript:$('#ccmCustomTemplateForm').get(0).submit()" class="ccm-button-right accept"><span>Update</span></a>
	</div>
	<? } ?>
</form>