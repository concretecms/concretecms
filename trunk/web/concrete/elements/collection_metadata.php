<?
defined('C5_EXECUTE') or die(_("Access Denied."));
global $c;
Loader::model('collection_types');
Loader::model('collection_attributes');
$dt = Loader::helper('form/date_time');

if ($cp->canAdminPage()) {
	$ctArray = CollectionType::getList();
}
?>
<div class="ccm-pane-controls">
<form method="post" name="permissionForm" id="ccmMetadataForm" action="<?=$c->getCollectionAction()?>">
			<input type="hidden" name="rel" value="<?=$_REQUEST['rel']?>" />

	<script type="text/javascript">

		function addOption(akID) {
			akOptions = document.getElementById("akID" + akID);
			akValue = document.getElementById("TEXTakID" + akID).value;
			akValue = akValue.replace(/^\s*|\s*$/g,"");
			if (akValue) {
				i = akOptions.length;
				akOptions.options[i] = new Option(akValue, akValue);
				akOptions.options[i].selected = true;		
			}
		}
		
		function ccm_triggerSelectUser(uID, uName) {
			$('#ccm-uID').val(uID);
			$('#ccm-uName').html(uName);
		}
		
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
	</script>
	
	<style>
	.ccm-field-meta #newAttrValueRows{ margin-top:4px; }
	.ccm-field-meta .newAttrValueRow{margin-top:4px}	
	.ccm-field-meta input.faint{ color:#999 }
	</style>
	
	<h1><?=t('Page Properties')?></h1>
	
	<div id="ccm-required-meta">
	<h2><?=t('Standard Information')?></h2></td>

	
	<div class="ccm-field-one">
	<label><?=t('Name')?></label> <input type="text" name="cName" value="<?=$c->getCollectionName()?>" class="ccm-input-text">
	</div>
	
	<div class="ccm-field-two">
	<label><?=t('Alias')?></label> <? if (!$c->isGeneratedCollection()) { ?><input s type="text" name="cHandle" class="ccm-input-text" value="<?=$c->getCollectionHandle()?>" id="cHandle"><input type="hidden" name="oldCHandle" value="<?=$c->getCollectionHandle()?>"><? } else { ?><?=$c->getCollectionHandle()?><? } ?>
	</div>
	
	
	<div class="ccm-field-one">
	
	<label><?=t('Public Date/Time')?></label> 
	<? 
	print $dt->datetime('cDatePublic', $c->getCollectionDatePublic()); ?>
	</div>
	
	<div class="ccm-field-two">
	<label><?=t('Owner')?></label>
		<? 
		$ui = UserInfo::getByID($c->getCollectionUserID());
		if (is_object($ui)) {
			$currentUName = $ui->getUserName();
		} else {
			$currentUName = "(None)";
		}
		print '<div style="padding-top: 4px;font-size: 12px"><span id="ccm-uName">' . $currentUName . '</span>';
		if ($cp->canAdminPage()) { ?>
		(<a href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/select_user.php" id="ccm-edit-page-user" dialog-modal="false" dialog-width="600" dialog-height="400" dialog-title="<?=t('Choose User')?>"><?=t('Edit')?></a>)
		<input type="hidden" name="uID" value="<?=$c->getCollectionUserID()?>" id="ccm-uID" />
		
		<script type="text/javascript">$(function() {
			$("#ccm-edit-page-user").dialog();
		})</script>
		<? } ?>
		</div>

	</div>
		
	
	<div class="ccm-field">
	<label><?=t('Description')?></label> <textarea name="cDescription" class="ccm-input-text" style="width: 570px; height: 50px"><?=$c->getCollectionDescription()?></textarea>
	</div>
	
<?
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
	<h2><?=t('Custom Fields')?> <select id="ccm-meta-custom-fields">
		<option value="">** <?=t('Add Field')?></option>
		<? $cAttributes = CollectionAttributeKey::getList(); 
		foreach($cAttributes as $ck) { 
			if (!in_array($ck->getCollectionAttributeKeyID(), $usedKeysCombined)) {?>
			<option value="<?=$ck->getCollectionAttributeKeyID()?>"><?=$ck->getCollectionAttributeKeyName()?></option>
		<? }
		
		}?>
	</select></h2><br/>
	
	<? 
		$al = Loader::helper('concrete/asset_library');


		foreach($cAttributes as $ak) {
			$caValue = $c->getCollectionAttributeValue($ak); ?>
		
		<div class="ccm-field-meta" id="ccm-field-ak<?=$ak->getCollectionAttributeKeyID()?>" <? if (!in_array($ak->getCollectionAttributeKeyID(), $usedKeysCombined)) { ?> style="display: none" <? } ?>>
		<input type="hidden" id="ccm-meta-field-selected<?=$ak->getCollectionAttributeKeyID()?>" name="selectedAKIDs[]" value="<? if (!in_array($ak->getCollectionAttributeKeyID(), $usedKeysCombined)) { ?>0<? } else { ?><?=$ak->getCollectionAttributeKeyID()?><? } ?>" />
		
		<? if (!in_array($ak->getCollectionAttributeKeyID(), $requiredKeys)) { ?>
			<a href="javascript:void(0)" class="ccm-meta-close" ccm-meta-name="<?=$ak->getCollectionAttributeKeyName()?>" id="ccm-remove-field-ak<?=$ak->getCollectionAttributeKeyID()?>"><?=t('Remove Field')?></a>
		<? } ?>
		<label><?=$ak->getCollectionAttributeKeyName()?></label>
			<?
			$akType=$ak->getCollectionAttributeKeyType();
			switch($akType) {
				case "SELECT":
					$options = explode("\n", $ak->getCollectionAttributeKeyValues()); 
					$caValues=explode("\n",$caValue); 
					?>
					<select style="width: 150px" name="akID_<?=$ak->getCollectionAttributeKeyID()?>">
						<option value="">** NONE</option>
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
	
	<input type="hidden" name="update_metadata" value="1" />
	<input type="hidden" name="processCollection" value="1">
	<div class="ccm-spacer">&nbsp;</div>
</form>
</div>
	<div class="ccm-buttons">
<!--	<a href="javascript:void(0)" onclick="ccm_hidePane()" class="ccm-button-left cancel"><span><em class="ccm-button-close">Cancel</em></span></a>//-->
	<a href="javascript:void(0)" onclick="$('#ccmMetadataForm').get(0).submit()" class="ccm-button-right accept"><span><?=t('Save')?></span></a>
	</div>	

<? Loader::element('block_al'); ?>