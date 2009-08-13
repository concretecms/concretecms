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
				<input type="button" onClick="ccmAttributesHelper.editValue('<?=addslashes($akSelectValueID)?>')" value="<?=t('Edit')?>" />
				<input type="button" onClick="ccmAttributesHelper.deleteValue('<?=addslashes($akSelectValueID)?>')" value="<?=t('Delete')?>" />
			</div>			
			<span onClick="ccmAttributesHelper.editValue('<?=addslashes($akSelectValueID)?>')" id="akSelectValueStatic_<?=$akSelectValueID?>" class="leftCol"><?=$akSelectValue ?></span>
		</div>
		<div id="akSelectValueEdit_<?=$akSelectValueID?>" style="display:none">
			<div class="rightCol">
				<input type="button" onClick="ccmAttributesHelper.editValue('<?=addslashes($akSelectValueID)?>')" value="<?=t('Cancel')?>" />
				<input type="button" onClick="ccmAttributesHelper.changeValue('<?=addslashes($akSelectValueID)?>')" value="<?=t('Save')?>" />
			</div>		
			<span class="leftCol">
				<input name="akSelectValueOriginal_<?=$akSelectValueID?>" type="hidden" value="<?=$akSelectValue?>" />
				<? if (is_object($v) && $v->getSelectAttributeOptionTemporaryID() == false) { ?>
					<input id="akSelectValueExistingOption_<?=$akSelectValueID?>" name="akSelectValueExistingOption_<?=$akSelectValueID?>" type="hidden" value="<?=$akSelectValueID?>" />
				<? } else { ?>
					<input id="akSelectValueNewOption_<?=$akSelectValueID?>" name="akSelectValueNewOption_<?=$akSelectValueID?>" type="hidden" value="<?=$akSelectValueID?>" />
				<? } ?>
				<input id="akSelectValueField_<?=$akSelectValueID?>" name="akSelectValue_<?=$akSelectValueID?>" type="text" value="<?=$akSelectValue?>" size="20" 
				  onkeypress="ccmAttributesHelper.addEnterClick(event,function(){ccmAttributesHelper.changeValue('<?=addslashes($akSelectValueID)?>')})" />
			</span>		
		</div>	
		<div class="ccm-spacer">&nbsp;</div>
<? } ?>

<table class="entry-form" cellspacing="1" cellpadding="0">
<tr>
	<td class="subheader" style="width:33%"><?=t('Multiple Values')?></td>
	<td class="subheader" style="width:34%"><?=t('User Submissions')?></td>
	<td class="subheader" style="width:33%"><?=t('Option Order')?></td>

</tr>
<tr>
	<td><?=$form->checkbox('akSelectAllowMultipleValues', 1, $akSelectAllowMultipleValues)?>
	<?=t('Allow multiple options to be chosen.')?>
	</td>
	<td><?=$form->checkbox('akSelectAllowOtherValues', 1, $akSelectAllowOtherValues)?>
	<?=t('Allow users to add to this list.')?>	
	</td>
	<? 
	$displayOrderOptions = array(
		'display_asc' => t('Display Order'),
		'alpha_asc' => t('Alphabetical'),
		'popularity_desc' => t('Most Popular First')
	);
	?>
	<td><?=$form->select('akSelectOptionDisplayOrder', $displayOrderOptions, $akSelectOptionDisplayOrder)?></td>

</tr>
<tr>
	<td colspan="3" class="subheader"><?=t('Values')?></td>
</tr>
<tr>
	<td colspan="3">
	<div id="attributeValuesInterface">
	<input type="hidden" id="akSelectValueSortableTarget" name="akSelectValueSortableTarget" value="<?=$this->action('sort_options', 'test')?>" />
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
		<div id="akSelectValueWrap_<?=$akSelectValueID?>" class="akSelectValueWrap <? if ($akSelectOptionDisplayOrder == 'display_asc') { ?> akSelectValueWrapSortable <? } ?>">
			<?=getAttributeOptionHTML( $v )?>
		</div>
	<? } ?>
	</div>
	<div class="ccm-spacer"></div>
	
	<div id="akSelectValueWrapTemplate" class="akSelectValueWrap" style="display:none">
		<?=getAttributeOptionHTML('TEMPLATE') ?>
	</div>
	<div class="ccm-spacer"></div>
	
	<div id="addAttributeValueWrap"> 
		<input id="akSelectValueFieldNew" name="akSelectValueNew" type="text" value="<?=$defaultNewOptionNm ?>" size="40" class="faint" 
		onfocus="ccmAttributesHelper.clrInitTxt(this,'<?=$defaultNewOptionNm ?>','faint',0)" 
		onblur="ccmAttributesHelper.clrInitTxt(this,'<?=$defaultNewOptionNm ?>','faint',1)"
		onkeypress="ccmAttributesHelper.addEnterClick(event,function(){ccmAttributesHelper.saveNewOption()})"
		 /> 
		<input type="button" onClick="ccmAttributesHelper.saveNewOption(); $('#ccm-attribute-key-form').unbind()" value="<?=t('Add') ?>" />
	</div>
	
	<? if ($attributeType == 'page') { ?>
	<div id="allowOtherValuesWrap" style="display:<?=($akType != 'SELECT' && $akType != 'SELECT_MULTIPLE')?'none':'block' ?>">
		<input type="checkbox" name="akAllowOtherValues" style="vertical-align: middle" <? if ($akAllowOtherValues) { ?> checked <? } ?> /> <?=t('Allow users to add to this list.')?>
	</div>
	<? } ?>

</div>
	</td>
</tr>
</table>

<? if ($akSelectOptionDisplayOrder == 'display_asc') { ?>
<script type="text/javascript">
$(function() {
	ccmAttributesHelper.makeSortable();
});
</script>
<? } ?>