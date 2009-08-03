<?

function getAttributeOptionHTML($akSelectValue="TEMPLATE"){ 
		$akSelectValueClean=str_replace(' ','',TextHelper::filterNonAlphaNum($akSelectValue));
		if($akSelectValue=='TEMPLATE') $akSelectValueClean='TEMPLATE_CLEAN'
		?>
		<div id="akSelectValueDisplay_<?=$akSelectValueClean?>" >
			<div class="rightCol">
				<a onClick="ccmAttributesHelper.editValue('<?=addslashes($akSelectValueClean)?>')"><?=t('Edit')?></a> |
				<a onClick="ccmAttributesHelper.deleteValue('<?=addslashes($akSelectValueClean)?>')"><?=t('Delete')?></a>
			</div>			
			<span onClick="ccmAttributesHelper.editValue('<?=addslashes($akSelectValueClean)?>')" id="akSelectValueStatic_<?=$akSelectValueClean?>" class="leftCol"><?=$akSelectValue ?></span>
		</div>
		<div id="akSelectValueEdit_<?=$akSelectValueClean?>" style="display:none">
			<div class="rightCol">
				<a onClick="ccmAttributesHelper.editValue('<?=addslashes($akSelectValueClean)?>')"><?=t('Cancel')?></a> | 
				<a onClick="ccmAttributesHelper.changeValue('<?=addslashes($akSelectValueClean)?>')"><?=t('Save')?></a>
			</div>		
			<span class="leftCol">
				<input name="akSelectValueOriginal_<?=$akSelectValueClean?>" type="hidden" value="<?=$akSelectValue?>" />
				<input id="akSelectValueField_<?=$akSelectValueClean?>" name="akSelectValue_<?=$akSelectValueClean?>" type="text" value="<?=$akSelectValue?>" size="20" 
				  onkeypress="ccmAttributesHelper.addEnterClick(event,function(){ccmAttributesHelper.changeValue('<?=addslashes($akSelectValueClean)?>')})" />
			</span>		
		</div>	
		<div class="ccm-spacer">&nbsp;</div>
<? } ?>

<table class="entry-form" cellspacing="1" cellpadding="0">
<tr>
	<td class="subheader" style="width:50%"><?=t('Multiple Values')?></td>
	<td class="subheader" style="width:50%"><?=t('User Submissions')?></td>
</tr>
<tr>
	<td><?=$form->checkbox('akSelectAllowMultipleValues', 1, false)?>
	<?=t('Allow multiple options to be chosen.')?>
	</td>
	<td><?=$form->checkbox('akSelectAllowOtherValues', 1, false)?>
	<?=t('Allow users to add to this list.')?>	
	</td>
</tr>
<tr>
	<td colspan="2" class="subheader"><?=t('Values')?></td>
</tr>
<tr>
	<td colspan="2">
	<div id="attributeValuesInterface">

	<div id="attributeValuesWrap">
	<?
	Loader::helper('text');
	if(!is_array($akSelectValues)) $akSelectValues=explode("\n",$akSelectValues);
	foreach($akSelectValues as $akSelectValue){ 
		$akSelectValueClean=str_replace(' ','',TextHelper::filterNonAlphaNum($akSelectValue));
		if(!strlen(trim($akSelectValue))) continue;		
		?>
		<div id="akSelectValueWrap_<?=$akSelectValueClean?>" class="akSelectValueWrap">
			<?=getAttributeOptionHTML( $akSelectValue )?>
		</div>
	<? } ?>
	</div>
	<div class="ccm-spacer"></div>
	
	<div id="akSelectValueWrapTemplate" class="akSelectValueWrap" style="display:none">
		<?=getAttributeOptionHTML() ?>
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