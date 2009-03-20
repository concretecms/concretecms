<script>
$(function() {
	$("#ccm-meta-custom-fields").change(function() {
		if ($(this).val() != "" && typeof($(this).val()) != undefined) {
			var thisField = $(this).val();
			$("#ccm-field-ak" + $(this).val()).show();
			this.options[this.selectedIndex] = null;
			this.selectedIndex = 0;
			
			$("#ccm-meta-field-selected" + thisField).val(thisField);
		}
	});
	
	$("a.ccm-meta-close").click(function() {
		var thisField = $(this).attr('id').substring(19);
		var thisName = $(this).attr('ccm-meta-name');
		$("#ccm-meta-field-selected" + thisField).val(0);
		$("#ccm-field-ak" + thisField).hide();
		
		// add it back to the select menu
		$("#ccm-meta-custom-fields").each(function() {
			this.options[this.options.length] = new Option(thisName, thisField);
		});
				
	});

	$("a.ccm-meta-path-add").click(function(ev) { ccmPathHelper.add(ev.target) });
	$("a.ccm-meta-path-del").click(function(ev) { ccmPathHelper.del(ev.target) });
});

var ccmAttributeValuesHelper={  
	add:function(akID){
		var newRow=document.createElement('div');
		newRow.className='newAttrValueRow';
		newRow.innerHTML='<input name="akID_'+akID+'[]" type="text" value="" /> ';
		newRow.innerHTML+='<a onclick="ccmAttributeValuesHelper.remove(this)">[X]</a>';
		$('#newAttrValueRows').append(newRow);				
	},
	remove:function(a){
		$(a.parentNode).remove();			
	},	
	clrInitTxt:function(field,initText,removeClass,blurred){
		if(blurred && field.value==''){
			field.value=initText;
			$(field).addClass(removeClass);
			return;	
		}
		if(field.value==initText) field.value='';
		if($(field).hasClass(removeClass)) $(field).removeClass(removeClass);
	}
}

var ccmPathHelper={
	add:function(field){
		var parent = $(field).parent();
		var clone = parent.clone();
		clone.children().each(function() {
			if (this.id != undefined  && (i = this.id.search("-add-")) != -1) {
				this.id = this.id.substr(0, i) + "-add-" + (parseInt(this.id.substr(i+5)) + 1);
			}
			if (this.name != undefined && (i = this.name.search("-add-")) != -1) {
				this.name = this.name.substr(0, i) + "-add-" + (parseInt(this.name.substr(i+5)) + 1);
			}
			if (this.type == "text") {
				this.value = "";
			}
		});
    	$(field).replaceWith('<a href="javascript:void(0)" class="ccm-meta-path-del">Remove Path</a>');
		clone.appendTo(parent.parent());

		$("a.ccm-meta-path-add,a.ccm-meta-path.del").unbind('click');
		$("a.ccm-meta-path-add").click(function(ev) { ccmPathHelper.add(ev.target) });
		$("a.ccm-meta-path-del").click(function(ev) { ccmPathHelper.del(ev.target) });
	},
	del:function(field){
		$(field).parent().remove();
	}
}
</script>


<div id="ccm-metadata-fields">


	<?
	$dt = Loader::helper('form/date_time');
	
	$requiredKeys = array();
	$usedKeys = array();
	if ($c->getCollectionTypeID() > 0) {
		$cto = CollectionType::getByID($c->getCollectionTypeID());
		$aks = $cto->getAvailableAttributeKeys();
		foreach($aks as $ak) {
			$requiredKeys[] = $ak->getCollectionAttributeKeyID();
		}
	}
	$setAttribs = $c->getSetCollectionAttributes();
	foreach($setAttribs as $ak) {
		$usedKeys[] = $ak->getCollectionAttributeKeyID();
	}
	$usedKeysCombined = array_merge($requiredKeys, $usedKeys);
	
	?>

	<h2>
		<?=t('Custom Fields')?> 
		<select id="ccm-meta-custom-fields">
			<option value="">** <?=t('Add Field')?></option>
			<? $cAttributes = CollectionAttributeKey::getList(); 
			foreach($cAttributes as $ck) { 
				if (!in_array($ck->getCollectionAttributeKeyID(), $usedKeysCombined) || $c->getCollectionTypeID()==0) {?>
				<option value="<?=$ck->getCollectionAttributeKeyID()?>"><?=$ck->getCollectionAttributeKeyName()?></option>
			<? }
			
			}?>
		</select>
	</h2><br/>

	<? 
		$al = Loader::helper('concrete/asset_library');


		foreach($cAttributes as $ak) {
			$caValue = $c->getCollectionAttributeValue($ak); ?>
		
		<div class="ccm-field-meta" id="ccm-field-ak<?=$ak->getCollectionAttributeKeyID()?>" <? if (!in_array($ak->getCollectionAttributeKeyID(), $usedKeysCombined)) { ?> style="display: none" <? } ?>>
		<input type="hidden" class="ccm-meta-field-selected" id="ccm-meta-field-selected<?=$ak->getCollectionAttributeKeyID()?>" name="selectedAKIDs[]" value="<? if (!in_array($ak->getCollectionAttributeKeyID(), $usedKeysCombined)) { ?>0<? } else { ?><?=$ak->getCollectionAttributeKeyID()?><? } ?>" />
		
		<a href="javascript:void(0)" class="ccm-meta-close" ccm-meta-name="<?=$ak->getCollectionAttributeKeyName()?>" id="ccm-remove-field-ak<?=$ak->getCollectionAttributeKeyID()?>"
		  style="display:<?=(!in_array($ak->getCollectionAttributeKeyID(), $requiredKeys))?'block':'none'?>" ><?=t('Remove Field')?></a>

		
		<label><?=$ak->getCollectionAttributeKeyName()?></label>
			<?
			$akType=$ak->getCollectionAttributeKeyType(); 
			
			switch($akType) {
				case "SELECT":
					$options = explode("\n", $ak->getCollectionAttributeKeyValues()); 
					$caValues=explode("\n",$caValue); 
					?>
					<select style="width: 150px" name="akID_<?=$ak->getCollectionAttributeKeyID()?>">
						<option value="">** <?=t('None')?></option>
						<? foreach($options as $val) {
							$val = trim($val);
							print '<option value="' . $val . '"';
							if ( in_array($val, $caValues) ) { 
								 print " selected";
							}						
							print '>' . $val . '</option>';
						} ?>
					</select>
					
					<? if( $ak->getAllowOtherValues() ){ ?> 
						
						<input name="akID_<?=$ak->getCollectionAttributeKeyID()?>_other" type="text" class="faint"
						value="<?=CollectionAttributeKey::getNewValueEmptyFieldTxt() ?>"
						
						onfocus="ccmAttributeValuesHelper.clrInitTxt(this,'<?=CollectionAttributeKey::getNewValueEmptyFieldTxt() ?>','faint',0)"  
						onblur="ccmAttributeValuesHelper.clrInitTxt(this,'<?=CollectionAttributeKey::getNewValueEmptyFieldTxt() ?>','faint',1)" 
						 />
					<? } ?>					
					
					<?
					break;
				case "SELECT_MULTIPLE":
					$options = explode("\n", $ak->getCollectionAttributeKeyValues()); 
					$caValues=explode("\n",$caValue);
					?>
					
					<div> 
					<?  foreach($options as $val) { ?>
						<div>
						<input name="akID_<?=$ak->getCollectionAttributeKeyID()?>[]" type="checkbox" value="<?=str_replace('"','\"',$val)?>" <?=( in_array($val, $caValues) )?'checked':''?> />
						<?=$val ?>
						</div>
					<? } ?>
					</div>
					
					<? if( $ak->getAllowOtherValues() ){ ?>
						<div id="newAttrValueRows">
						</div>
						<div><a onclick="ccmAttributeValuesHelper.add(<?=intval($ak->getCollectionAttributeKeyID())?>)">
							<?=t('Add Another Option')?> +</a>
						</div>
					<? } ?>
							
					<?

				case "NUMBER":?>
					<input name="akID_<?=$ak->getCollectionAttributeKeyID()?>" type="text" value="<?=$caValue ?>" size="10" />
					<?
					break;	
				case "RATING":
					$rt = Loader::helper('form/rating');
					print $rt->rating('akID_' . $ak->getCollectionAttributeKeyID(), $caValue); 
					break;
				case "IMAGE_FILE": 
					$bf = null; 
					if (is_object($caValue)) {
						$bf = $caValue;
					}
					print $al->file('ccm-file-akID-' . $ak->getCollectionAttributeKeyID(), 'akID_' . $ak->getCollectionAttributeKeyID(), t('Choose File'), $bf);?>
				<?
					break;
				case "BOOLEAN":?>
					<input type="checkbox" <? if ($caValue == 1) { ?> checked <? } ?> name="akID_<?=$ak->getCollectionAttributeKeyID()?>" value="1" /> <?=t('Yes')?>
					<?
					break;
				case "DATE":
					print $dt->datetime('akID_' . $ak->getCollectionAttributeKeyID(), $caValue);
					break;
				default: // text ?>		
				
				<textarea style="width: 100%; height: 40px" name="akID_<?=$ak->getCollectionAttributeKeyID()?>"><?=$caValue?></textarea>
				
				<? break;
			} ?>
			
			</div>
		<? } ?>	

</div>
