<?

function getAttributeOptionHTML($v){ 
	if ($v == 'TEMPLATE') {
		$akSelectValueID = 'TEMPLATE_CLEAN';
		$akSelectValue = 'TEMPLATE';
	} else {
		if ($v->getSelectAttributeOptionTemporaryID() != false) {
			$akSelectValueID = $v->getSelectAttributeOptionTemporaryID();
		} else {
			$akSelectValueID = $v->getSelectAttributeOptionID();
		}
		$akSelectValue = $v->getSelectAttributeOptionValue();
	}
		?>
		<div id="akSelectValueDisplay_<?=$akSelectValueID?>" >
			<div class="rightCol">
				<input class="btn btn-primary" type="button" onClick="ccmAttributesHelper.editValue('<?=addslashes($akSelectValueID)?>')" value="<?=t('Edit')?>" />
				<input class="btn btn-danger" type="button" onClick="ccmAttributesHelper.deleteValue('<?=addslashes($akSelectValueID)?>')" value="<?=t('Delete')?>" />
			</div>			
			<span onClick="ccmAttributesHelper.editValue('<?=addslashes($akSelectValueID)?>')" id="akSelectValueStatic_<?=$akSelectValueID?>" class="leftCol"><?=$akSelectValue ?></span>
		</div>
		<div id="akSelectValueEdit_<?=$akSelectValueID?>" style="display:none">
			<span class="leftCol">
				<input name="akSelectValueOriginal_<?=$akSelectValueID?>" type="hidden" value="<?=$akSelectValue?>" />
				<? if (is_object($v) && $v->getSelectAttributeOptionTemporaryID() == false) { ?>
					<input id="akSelectValueExistingOption_<?=$akSelectValueID?>" name="akSelectValueExistingOption_<?=$akSelectValueID?>" type="hidden" value="<?=$akSelectValueID?>" />
				<? } else { ?>
					<input id="akSelectValueNewOption_<?=$akSelectValueID?>" name="akSelectValueNewOption_<?=$akSelectValueID?>" type="hidden" value="<?=$akSelectValueID?>" />
				<? } ?>
				<input id="akSelectValueField_<?php echo $akSelectValueID?>" onkeydown="ccmAttributesHelper.keydownHandler(event);" class="akSelectValueField form-control" data-select-value-id="<?php echo $akSelectValueID; ?>" name="akSelectValue_<?php echo $akSelectValueID?>" type="text" value="<?php echo $akSelectValue?>" size="40" />
			</span>		
			<div class="rightCol">
				<input class="btn btn-default" type="button" onClick="ccmAttributesHelper.editValue('<?=addslashes($akSelectValueID)?>')" value="<?=t('Cancel')?>" />
				<input class="btn btn-success" type="button" onClick="ccmAttributesHelper.changeValue('<?=addslashes($akSelectValueID)?>')" value="<?=t('Save')?>" />
			</div>		
		</div>	
		<div class="ccm-spacer">&nbsp;</div>
<? } ?>

<fieldset class="ccm-attribute ccm-attribute-select">
<legend><?=t('Select Options')?></legend>

<div class="form-group">
    <label><?=t("Multiple Values")?></label>
    <div class="checkbox">
        <label>
            <?=$form->checkbox('akSelectAllowMultipleValues', 1, $akSelectAllowMultipleValues)?> <span><?=t('Allow multiple options to be chosen.')?></span>
        </label>
    </div>
</div>

<div class="form-group">
    <label><?=t("User Submissions")?></label>
    <div class="checkbox">
        <label>
            <?=$form->checkbox('akSelectAllowOtherValues', 1, $akSelectAllowOtherValues)?> <span><?=t('Allow users to add to this list.')?></span>
        </label>
    </div>
</div>

<div class="form-group">
<label for="akSelectOptionDisplayOrder"><?=t("Option Order")?></label>
	<?
	$displayOrderOptions = array(
		'display_asc' => t('Display Order'),
		'alpha_asc' => t('Alphabetical'),
		'popularity_desc' => t('Most Popular First')
	);
	?>

	<?=$form->select('akSelectOptionDisplayOrder', $displayOrderOptions, $akSelectOptionDisplayOrder)?>
</div>

<div class="clearfix">
<label><?=t('Values')?></label>
<div class="input">
	<div id="attributeValuesInterface">
	<div id="attributeValuesWrap">
	<?
	Loader::helper('text');
	foreach($akSelectValues as $v) { 
		if ($v->getSelectAttributeOptionTemporaryID() != false) {
			$akSelectValueID = $v->getSelectAttributeOptionTemporaryID();
		} else {
			$akSelectValueID = $v->getSelectAttributeOptionID();
		}
		?>
		<div id="akSelectValueWrap_<?=$akSelectValueID?>" class="akSelectValueWrap akSelectValueWrapSortable">
			<?=getAttributeOptionHTML( $v )?>
		</div>
	<? } ?>
	</div>
	
	<div id="akSelectValueWrapTemplate" class="akSelectValueWrap" style="display:none">
		<?=getAttributeOptionHTML('TEMPLATE') ?>
	</div>
	
	<div id="addAttributeValueWrap" class="form-inline">
		<input id="akSelectValueFieldNew" name="akSelectValueNew" type="text" value="<?=$defaultNewOptionNm ?>" size="40"  class="form-control"
		onfocus="ccmAttributesHelper.clrInitTxt(this,'<?=$defaultNewOptionNm ?>','faint',0)" 
		onblur="ccmAttributesHelper.clrInitTxt(this,'<?=$defaultNewOptionNm ?>','faint',1)"
		onkeypress="ccmAttributesHelper.keydownHandler(event);"
		 /> 
		<input class="btn btn-primary" type="button" onClick="ccmAttributesHelper.saveNewOption(); $('#ccm-attribute-key-form').unbind()" value="<?=t('Add') ?>" />
	</div>
	</div>

</div>
</div>


</fieldset>
<script type="text/javascript">
//<![CDATA[
$(function() {
	ccmAttributesHelper.makeSortable();
});
//]]>
</script>
