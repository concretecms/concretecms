<?
defined('C5_EXECUTE') or die("Access Denied.");
global $c;?>
<?
$form = Loader::helper('form');
$bt = BlockType::getByID($b->getBlockTypeID());
$templates = $bt->getBlockTypeComposerTemplates();
$txt = Loader::helper('text');
?>
<form method="post" id="ccmComposerCustomTemplateForm" action="<?=$b->getBlockUpdateComposerSettingsAction()?>&rcID=<?=intval($rcID) ?>">

	<strong><?=t('Composer')?></strong><br/>
	<?=$form->checkbox('bIncludeInComposer', 1, $b->isBlockIncludedInComposer())?> <?=t("Include block in Composer")?>
	<br/><br/>
	
	
	<strong><?=t('Block Name')?></strong><br/>
	<?=$form->text('bName', $b->getBlockName(), array('style' => 'width: 280px'))?>
	<br/><br/>
	
	<strong><?=t('Custom Composer Template')?></strong><br>
	
	<? if (count($templates) == 0) { ?>
	
		<?=t('There are no custom templates available.')?>

	<? } else { ?>
	
		<select name="cbFilename">
			<option value="">(<?=t('None selected')?>)</option>
			<? foreach($templates as $tpl) { ?>
				<option value="<?=$tpl?>" <? if ($b->getBlockComposerFilename() == $tpl) { ?> selected <? } ?>><?	
					if (strpos($tpl, '.') !== false) {
						print substr($txt->unhandle($tpl), 0, strrpos($tpl, '.'));
					} else {
						print $txt->unhandle($tpl);
					}
					?></option>		
			<? } ?>
		</select>
		
	<? } ?>
<?
$valt = Loader::helper('validation/token');
$valt->output();
?>

		<div class="ccm-buttons">
		<a href="#" class="ccm-dialog-close ccm-button-left cancel"><span><em class="ccm-button-close"><?=t('Cancel')?></em></span></a>
		<a href="javascript:void(0)" onclick="$('#ccmComposerCustomTemplateForm').submit()" class="ccm-button-right accept"><span><?=t('Update')?></span></a>
		</div>

</form>

<script type="text/javascript">
$(function() {
	$('#ccmComposerCustomTemplateForm').each(function() {
		ccm_setupBlockForm($(this), '<?=$b->getBlockID()?>', 'edit');
	});
});
</script>