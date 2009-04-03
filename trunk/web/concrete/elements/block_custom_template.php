<?
defined('C5_EXECUTE') or die(_("Access Denied."));
global $c;?>
<?
$bt = BlockType::getByID($b->getBlockTypeID());
$templates = $bt->getBlockTypeCustomTemplates();
$txt = Loader::helper('text');
?>
<form method="post" id="ccmCustomTemplateForm" action="<?=$b->getBlockUpdateInformationAction()?>">
	
	<strong><?=t('Custom Template')?></strong>:<br>
	
	<? if (count($templates) == 0) { ?>
	
		<?=t('There are no custom templates available.')?>
		<div class="ccm-buttons">
		<a href="#" class="ccm-dialog-close ccm-button-left cancel"><span><em class="ccm-button-close"><?=t('Cancel')?></em></span></a>
		</div>

	<? } else { ?>
	
		<select name="bFilename">
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
		<div class="ccm-buttons">
		<a href="#" class="ccm-dialog-close ccm-button-left cancel"><span><em class="ccm-button-close"><?=t('Cancel')?></em></span></a>
		<a href="javascript:$('#ccmCustomTemplateForm').get(0).submit()" class="ccm-button-right accept"><span><?=t('Update')?></span></a>
		</div>
		
	<? } ?>
<?
$valt = Loader::helper('validation/token');
$valt->output();
?>
</form>