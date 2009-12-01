<?
//THIS PAGE IS DEPREICATED

//Use elements/dashboard/attribute_values.php instead





//because duplicated code is evil.
function getAttributeOptionHTML($akValue="TEMPLATE"){ 
		$akValueClean=TextHelper::filterNonAlphaNum($akValue);
		if($akValue=='TEMPLATE') $akValueClean='TEMPLATE_CLEAN'
		?>
		<div id="akValueDisplay_<?=$akValueClean?>" >
			<div class="rightCol">
				<a onClick="ccmAttributesHelper.editValue('<?=addslashes($akValueClean)?>')"><?=t('Edit')?></a> |
				<a onClick="ccmAttributesHelper.deleteValue('<?=addslashes($akValueClean)?>')"><?=t('Delete')?></a>
			</div>			
			<span onClick="ccmAttributesHelper.editValue('<?=addslashes($akValueClean)?>')" id="akValueStatic_<?=$akValueClean?>" class="leftCol"><?=$akValue ?></span>
		</div>
		<div id="akValueEdit_<?=$akValueClean?>" style="display:none">
			<div class="rightCol">
				<a onClick="ccmAttributesHelper.editValue('<?=addslashes($akValueClean)?>')"><?=t('Cancel')?></a> | 
				<a onClick="ccmAttributesHelper.changeValue('<?=addslashes($akValueClean)?>')"><?=t('Save')?></a>
			</div>		
			<span class="leftCol">
				<input name="akValueOriginal_<?=$akValueClean?>" type="hidden" value="<?=$akValue?>" />
				<input id="akValueField_<?=$akValueClean?>" name="akValue_<?=$akValueClean?>" type="text" value="<?=$akValue?>" size="20" 
				  onkeypress="ccmAttributesHelper.addEnterClick(event,function(){ccmAttributesHelper.changeValue('<?=addslashes($akValueClean)?>')})" />
			</span>		
		</div>	
		<div class="ccm-spacer">&nbsp;</div>
<? } ?>

<div id="attributeValuesOffMsg" style="display:<?=($akType != 'SELECT' && $akType != 'SELECT_MULTIPLE')?'block':'none' ?>">
	<?=t('(Not Applicable)')?>
</div>

<div id="attributeValuesInterface" style="display:<?=($akType != 'SELECT' && $akType != 'SELECT_MULTIPLE')?'none':'block' ?>">

	<div id="attributeValuesWrap">
	<?
	Loader::helper('text');
	if(!is_array($akValues)) $akValues=explode("\n",$akValues);
	foreach($akValues as $akValue){ 
		if(!strlen(trim($akValue))) continue;
		?>
		<div id="akValueWrap_<?=$akValue?>" class="akValueWrap">
			<?=getAttributeOptionHTML( $akValue )?>
		</div>
	<? } ?>
	</div>
	<div class="ccm-spacer"></div>
	
	<div id="akValueWrapTemplate" class="akValueWrap" style="display:none">
		<?=getAttributeOptionHTML() ?>
	</div>
	<div class="ccm-spacer"></div>
	
	<div id="addAttributeValueWrap"> 
		<input id="akValueFieldNew" name="akValueNew" type="text" value="<?=$defaultNewOptionNm ?>" size="40" class="faint" 
		onfocus="ccmAttributesHelper.clrInitTxt(this,'<?=$defaultNewOptionNm ?>','faint',0)" 
		onblur="ccmAttributesHelper.clrInitTxt(this,'<?=$defaultNewOptionNm ?>','faint',1)"
		onkeypress="ccmAttributesHelper.addEnterClick(event,function(){ccmAttributesHelper.saveNewOption()})"
		 /> 
		<a onClick="ccmAttributesHelper.saveNewOption()"><?=t('Add') ?> +</a>
	</div>
	
	<div id="allowOtherValuesWrap" style="display:<?=($akType != 'SELECT' && $akType != 'SELECT_MULTIPLE')?'none':'block' ?>">
		<input type="checkbox" name="akAllowOtherValues" style="vertical-align: middle" <? if ($akAllowOtherValues) { ?> checked <? } ?> /> <?=t('Allow users to add to this list.')?>
	</div>

</div>