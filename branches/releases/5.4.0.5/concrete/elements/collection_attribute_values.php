<?php 
//THIS PAGE IS DEPREICATED

//Use elements/dashboard/attribute_values.php instead





//because duplicated code is evil.
function getAttributeOptionHTML($akValue="TEMPLATE"){ 
		$akValueClean=TextHelper::filterNonAlphaNum($akValue);
		if($akValue=='TEMPLATE') $akValueClean='TEMPLATE_CLEAN'
		?>
		<div id="akValueDisplay_<?php echo $akValueClean?>" >
			<div class="rightCol">
				<a onClick="ccmAttributesHelper.editValue('<?php echo addslashes($akValueClean)?>')"><?php echo t('Edit')?></a> |
				<a onClick="ccmAttributesHelper.deleteValue('<?php echo addslashes($akValueClean)?>')"><?php echo t('Delete')?></a>
			</div>			
			<span onClick="ccmAttributesHelper.editValue('<?php echo addslashes($akValueClean)?>')" id="akValueStatic_<?php echo $akValueClean?>" class="leftCol"><?php echo $akValue ?></span>
		</div>
		<div id="akValueEdit_<?php echo $akValueClean?>" style="display:none">
			<div class="rightCol">
				<a onClick="ccmAttributesHelper.editValue('<?php echo addslashes($akValueClean)?>')"><?php echo t('Cancel')?></a> | 
				<a onClick="ccmAttributesHelper.changeValue('<?php echo addslashes($akValueClean)?>')"><?php echo t('Save')?></a>
			</div>		
			<span class="leftCol">
				<input name="akValueOriginal_<?php echo $akValueClean?>" type="hidden" value="<?php echo $akValue?>" />
				<input id="akValueField_<?php echo $akValueClean?>" name="akValue_<?php echo $akValueClean?>" type="text" value="<?php echo $akValue?>" size="20" 
				  onkeypress="ccmAttributesHelper.addEnterClick(event,function(){ccmAttributesHelper.changeValue('<?php echo addslashes($akValueClean)?>')})" />
			</span>		
		</div>	
		<div class="ccm-spacer">&nbsp;</div>
<?php  } ?>

<div id="attributeValuesOffMsg" style="display:<?php echo ($akType != 'SELECT' && $akType != 'SELECT_MULTIPLE')?'block':'none' ?>">
	<?php echo t('(Not Applicable)')?>
</div>

<div id="attributeValuesInterface" style="display:<?php echo ($akType != 'SELECT' && $akType != 'SELECT_MULTIPLE')?'none':'block' ?>">

	<div id="attributeValuesWrap">
	<?php 
	Loader::helper('text');
	if(!is_array($akValues)) $akValues=explode("\n",$akValues);
	foreach($akValues as $akValue){ 
		if(!strlen(trim($akValue))) continue;
		?>
		<div id="akValueWrap_<?php echo $akValue?>" class="akValueWrap">
			<?php echo getAttributeOptionHTML( $akValue )?>
		</div>
	<?php  } ?>
	</div>
	<div class="ccm-spacer"></div>
	
	<div id="akValueWrapTemplate" class="akValueWrap" style="display:none">
		<?php echo getAttributeOptionHTML() ?>
	</div>
	<div class="ccm-spacer"></div>
	
	<div id="addAttributeValueWrap"> 
		<input id="akValueFieldNew" name="akValueNew" type="text" value="<?php echo $defaultNewOptionNm ?>" size="40" class="faint" 
		onfocus="ccmAttributesHelper.clrInitTxt(this,'<?php echo $defaultNewOptionNm ?>','faint',0)" 
		onblur="ccmAttributesHelper.clrInitTxt(this,'<?php echo $defaultNewOptionNm ?>','faint',1)"
		onkeypress="ccmAttributesHelper.addEnterClick(event,function(){ccmAttributesHelper.saveNewOption()})"
		 /> 
		<a onClick="ccmAttributesHelper.saveNewOption()"><?php echo t('Add') ?> +</a>
	</div>
	
	<div id="allowOtherValuesWrap" style="display:<?php echo ($akType != 'SELECT' && $akType != 'SELECT_MULTIPLE')?'none':'block' ?>">
		<input type="checkbox" name="akAllowOtherValues" style="vertical-align: middle" <?php  if ($akAllowOtherValues) { ?> checked <?php  } ?> /> <?php echo t('Allow users to add to this list.')?>
	</div>

</div>