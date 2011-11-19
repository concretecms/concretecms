<?
defined('C5_EXECUTE') or die("Access Denied.");
global $c;?>
<?
$form = Loader::helper('form');
$bt = BlockType::getByID($b->getBlockTypeID());
$templates = $bt->getBlockTypeComposerTemplates();
$txt = Loader::helper('text');
?>
<div class="ccm-ui">

<form method="post" class="form-stacked" id="ccmComposerCustomTemplateForm" action="<?=$b->getBlockUpdateComposerSettingsAction()?>&rcID=<?=intval($rcID) ?>">

	<div class="clearfix">
	<div class="input">
	<ul class="inputs-list">
	<li><label><?=$form->checkbox('bIncludeInComposer', 1, $b->isBlockIncludedInComposer())?> <span><?=t("Include block in Composer")?></span></label></li>
	</ul>
	</div>
	</div>
	
	<div class="clearfix">
	<?=$form->label('bName', t('Block Name'))?>
	<div class="input">
	<?=$form->text('bName', $b->getBlockName(), array('style' => 'width: 280px'))?>
	</div>
	</div>


	<? if (count($templates) > 0) { ?>

	<div class="clearfix">
	<?=$form->label('cbFilename', t('Custom Composer Template'))?>
	<div class="input">
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
	</div>
	</div>
	
	<? } ?>
<?
$valt = Loader::helper('validation/token');
$valt->output();
?>

		<div class="dialog-buttons">
		<a href="#" class="ccm-dialog-close ccm-button-left cancel btn"><?=t('Cancel')?></a>
		<a href="javascript:void(0)" onclick="$('#ccmComposerCustomTemplateForm').submit()" class="ccm-button-right accept primary btn"><?=t('Update')?></a>
		</div>

</form>
</div>

<script type="text/javascript">
$(function() {
	$('#ccmComposerCustomTemplateForm').each(function() {
		ccm_setupBlockForm($(this), '<?=$b->getBlockID()?>', 'edit');
	});
});
</script>