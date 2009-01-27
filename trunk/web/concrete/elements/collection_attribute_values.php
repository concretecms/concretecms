<?
//because duplicated code is evil.
function getAttributeOptionHTML($akValue="TEMPLATE"){ ?>
		<div id="akValueDisplay_<?=$akValue?>" >
			<div class="rightCol">
				<a onClick="ccmAttributesHelper.editValue('<?=addslashes($akValue)?>')"><?=t('Edit')?></a> |
				<a onClick="ccmAttributesHelper.deleteValue('<?=addslashes($akValue)?>')"><?=t('Delete')?></a>
			</div>			
			<span onClick="ccmAttributesHelper.editValue('<?=addslashes($akValue)?>')" id="akValueStatic_<?=$akValue?>" class="leftCol"><?=$akValue ?></span>
		</div>
		<div id="akValueEdit_<?=$akValue?>" style="display:none">
			<div class="rightCol">
				<a onClick="ccmAttributesHelper.editValue('<?=addslashes($akValue)?>')"><?=t('Cancel')?></a> | 
				<a onClick="ccmAttributesHelper.changeValue('<?=addslashes($akValue)?>')"><?=t('Save')?></a>
			</div>		
			<span class="leftCol">
				<input id="akValueField_<?=$akValue?>" name="akValue_<?=$akValue?>" type="text" value="<?=$akValue?>" size="20" />
			</span>		
		</div>	
		<div class="ccm-spacer">&nbsp;</div>
<? } ?>

<div id="attributeValuesWrap">
<?
if(!is_array($akValues)) $akValues=explode("\n",$akValues);
foreach($akValues as $akValue){ 
	if(!strlen(trim($akValue))) continue;
	?>
	<div id="akValueWrap_<?=$akValue?>" class="akValueWrap">
		<?=getAttributeOptionHTML($akValue)?>
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
	onblur="ccmAttributesHelper.clrInitTxt(this,'<?=$defaultNewOptionNm ?>','faint',1)" /> 
	<a onClick="ccmAttributesHelper.saveNewOption()"><?=t('Add') ?> +</a>
</div>

<div id="allowOtherValuesWrap" style="display:<?=($akType != 'SELECT' && $akType != 'SELECT_MULTIPLE')?'none':'block' ?>">
	<input type="checkbox" name="akAllowOtherValues" style="vertical-align: middle" <? if ($akAllowOtherValues) { ?> checked <? } ?> /> <?=t('Allow users to add to this list.')?>
</div>