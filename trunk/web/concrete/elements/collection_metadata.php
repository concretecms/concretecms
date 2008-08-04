<? global $c;
	Loader::model('collection_types');
	Loader::model('collection_attributes');

 if ($cp->canAdminPage()) {
	$ctArray = CollectionType::getList();
}
?>
<div class="ccm-pane-controls">
<form method="post" name="permissionForm" id="ccmMetadataForm" action="<?=$c->getCollectionAction()?>">
			<input type="hidden" name="rel" value="<?=$_REQUEST['rel']?>" />

	<script type="text/javascript">
		/*
		function makeAlias(value, formInputID) {
			alias = value.replace(/[&]/gi, "and");
			alias = alias.replace(/[\s|.]+/gi, "-");
			alias = alias.replace(/[^0-9A-Za-z-]/gi, "");
			alias = alias.replace(/--/gi, '-');
			alias = alias.toLowerCase();
			
			formObj = document.getElementById(formInputID);
			formObj.value = alias;
		}
		*/
		
		function checkHandle() {
			// checks to see if the handle you're submitting is different than original. If it is, we warn
			// that, if you're changing a high-level page, you're going to be waiting a little while while 
			// we re-gen the collection paths for all pages underneath
			
			if (document.forms['permissionForm'].cHandle.value != document.forms['permissionForm'].oldCHandle.value) {
				return (confirm('You have changed the alias for this page. This update may take awhile, while we re-generate paths.'));
			}
		}
		

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
		
	</script>
	
	<h1>Page Properties</h1>
	
	<div id="ccm-required-meta">
	<h2>Standard Information</h2></td>

	
	<div class="ccm-field-one">
	<label>Page Name</label> <input type="text" name="cName" value="<?=$c->getCollectionName()?>" class="ccm-input-text">
	</div>
	
	<div class="ccm-field-two">
	<label>Page Alias</label> <? if (!$c->isGeneratedCollection()) { ?><input s type="text" name="cHandle" class="ccm-input-text" value="<?=$c->getCollectionHandle()?>" id="cHandle"><input type="hidden" name="oldCHandle" value="<?=$c->getCollectionHandle()?>"><? } else { ?><?=$c->getCollectionHandle()?><? } ?>
	</div>
	
	
	<div class="ccm-field-one">
	
	<label>Public Date/Time</label> 
	<? 
	$dt = Loader::helper('form/date_time');
	print $dt->datetime('cDatePublic', $c->getCollectionDatePublic()); ?>
	</div>
	
	<div class="ccm-field-two">
	<label>Owned By</label>
		<? 
		$ui = UserInfo::getByID($c->getCollectionUserID());
		if (is_object($ui)) {
			$currentUName = $ui->getUserName();
		} else {
			$currentUName = "(None)";
		}
		print '<div style="padding-top: 4px;font-size: 12px"><span id="ccm-uName">' . $currentUName . '</span>';
		if ($cp->canAdminPage()) { ?>
		(<a href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/select_user.php" id="ccm-edit-page-user" dialog-modal="false" dialog-width="600" dialog-height="400" dialog-title="Choose User">edit</a>)
		<input type="hidden" name="uID" value="<?=$c->getCollectionUserID()?>" id="ccm-uID" />
		
		<script type="text/javascript">$(function() {
			$("#ccm-edit-page-user").dialog();
		})</script>
		<? } ?>
		</div>

	</div>
		
	
	<div class="ccm-field">
	<label>Page Description</label> <textarea name="cDescription" class="ccm-input-text" style="width: 570px; height: 50px"><?=$c->getCollectionDescription()?></textarea>
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
	<h2>Custom Fields <select id="ccm-meta-custom-fields">
		<option value="">** Add Field</option>
		<? $cAttributes = CollectionAttributeKey::getList(); 
		foreach($cAttributes as $ck) { 
			if (!in_array($ck->getCollectionAttributeKeyID(), $usedKeysCombined)) {?>
			<option value="<?=$ck->getCollectionAttributeKeyID()?>"><?=$ck->getCollectionAttributeKeyName()?></option>
		<? }
		
		}?>
	</select></h2><br/>
	
	<? 

		foreach($cAttributes as $ak) {
			$caValue = $c->getCollectionAttributeValue($ak); ?>
		
		<div class="ccm-field-meta" id="ccm-field-ak<?=$ak->getCollectionAttributeKeyID()?>" <? if (!in_array($ak->getCollectionAttributeKeyID(), $usedKeysCombined)) { ?> style="display: none" <? } ?>>
		<input type="hidden" id="ccm-meta-field-selected<?=$ak->getCollectionAttributeKeyID()?>" name="selectedAKIDs[]" value="<? if (!in_array($ak->getCollectionAttributeKeyID(), $usedKeysCombined)) { ?>0<? } else { ?><?=$ak->getCollectionAttributeKeyID()?><? } ?>" />
		
		<? if (!in_array($ak->getCollectionAttributeKeyID(), $requiredKeys)) { ?>
			<a href="javascript:void(0)" class="ccm-meta-close" ccm-meta-name="<?=$ak->getCollectionAttributeKeyName()?>" id="ccm-remove-field-ak<?=$ak->getCollectionAttributeKeyID()?>">Remove Field</a>
		<? } ?>
		<label><?=$ak->getCollectionAttributeKeyName()?></label>
			<? switch($ak->getCollectionAttributeKeyType()) {
				case "SELECT":
					$options = explode(',', $ak->getCollectionAttributeKeyValues()); ?>
					<select style="width: 150px" name="akID_<?=$ak->getCollectionAttributeKeyID()?>">
						<option value="">** NONE</option>
						<? foreach($options as $val) {
							$val = trim($val);
							print '<option value="' . $val . '"';
							if ($caValue == $val) { 
								print " selected";
							}						
							print '>' . $val . '</option>';
						} ?>
					</select>
					<?
					break;
				case "SELECT_ADD":
						$options = $ak->getPreviouslySelectedValues();
						$caValueArray = explode("[|]", $caValue);
						?>
						<div id="selectAdd<?=$ak->getCollectionAttributeKeyID()?>">
						<select style="width: 250px; height: 80px;" multiple name="akID_<?=$ak->getCollectionAttributeKeyID()?>[]" id="akID<?=$ak->getCollectionAttributeKeyID()?>">
							<? foreach($options as $val) {
								$val = trim($val);
								print '<option value="' . $val . '"';
								if (in_array($val, $caValueArray)) {
									print " selected";
								}	
								print '>' . $val . '</option>';
							}
						?>
						</select>
						<div id="addElement<?=$ak->getCollectionAttributeKeyID()?>_1">
						<input style="vertical-align: middle" type="text" style="width: 220px" id="TEXTakID<?=$ak->getCollectionAttributeKeyID()?>" />
						<input type="button" id="btn<?=$ak->getCollectionAttributeKeyID()?>_1" onclick="addOption(<?=$ak->getCollectionAttributeKeyID()?>)" value="+" style="font-size: 10px; vertical-align: middle" />
						</div>
						</div>
						<?
						break;
				case "BOOLEAN":?>
					<input type="checkbox" <? if ($caValue == 1) { ?> checked <? } ?> name="akID_<?=$ak->getCollectionAttributeKeyID()?>" value="1" /> Yes
					<?
					break;
				case "DATE":?>
					<input type="text" value="<?=$caValue?>" name="akID_<?=$ak->getCollectionAttributeKeyID()?>" id="akID_<?=$ak->getCollectionAttributeKeyID()?>" />
					<input type="button" name="" style="font-size: 10px; vertical-align: middle" value="calendar" onclick="popUpCalendar(this, document.getElementById('akID_<?=$ak->getCollectionAttributeKeyID()?>'), 'yyyy-mm-dd')">
					<?
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
	<a href="javascript:void(0)" onclick="$('#ccmMetadataForm').get(0).submit()" class="ccm-button-right accept"><span>Save</span></a>
	</div>	
