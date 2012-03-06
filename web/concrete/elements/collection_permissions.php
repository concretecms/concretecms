<?
defined('C5_EXECUTE') or die("Access Denied.");
Loader::model('collection_types');
$dh = Loader::helper('date');
$dt = Loader::helper('form/date_time');

if ($cp->canAdminPage()) {
	// if it's composer mode and we have a target, then we hack the permissions collection id
	if (isset($isComposer) && $isComposer) {
		$cd = ComposerPage::getByID($c->getCollectionID());
		if ($cd->isComposerDraft()) {
			if ($cd->getComposerDraftPublishParentID() > 0) {
				if ($cd->getCollectionInheritance() == 'PARENT') {
					$c->cParentID = $cd->getComposerDraftPublishParentID();
					$cpID = $c->getParentPermissionsCollectionID();
					$c->cInheritPermissionsFromCID = $cpID;
				}
			}
		}
	}

	$gl = new GroupList($c);
	$gArray = $gl->getGroupList();
	$ul = new UserInfoList($c);
	$ulArray = $ul->getUserInfoList();
	$ctArray = CollectionType::getList($c);
}
$saveMsg = t('Save permissions first.');
?>
<div class="ccm-ui">

<form method="post" id="ccmPermissionsForm" name="ccmPermissionsForm" action="<?=$c->getCollectionAction()?>">
<input type="hidden" name="rel" value="<?=$_REQUEST['rel']?>" />

	<script type="text/javascript">
		var activeSlate = "permissions";	
		function togglePermissionsGrid(value, doReset) {
			$("td." + activeSlate).hide();
			$("td." + value).show();
			$("th." + activeSlate).hide();
			$("th." + value).show();
			
			activeSlate = value;
			if (doReset) {
				$("#toggleGrid").get(0).selectedIndex = 0;
			}
		}
		
		function ccm_triggerSelectUser(uID, uName) {
			togglePermissionsGrid('permissions', true);
			rowValue = "uID:" + uID;
			existingRow = document.getElementById("_row:" + rowValue);		  
			if (!existingRow) {
				tbl = document.getElementById("ccmPermissionsTablePage");

				row = tbl.insertRow(-1); // insert at bottom of table. safari, wtf ?                            
				row.id = "_row:" + rowValue;
				
				ccm_setupGridStriping('ccmPermissionsTablePage');
				
				cells = new Array();
				target = 10;
				for (i = 0; i < target; i++) {
					cells[i] = row.insertCell(i);
					if (i < 7 && i > 0) {
						cells[i].className = "permissions";
					} else if (i == 7) {
						cells[i].className = "subpage";
					} else {
						cells[i].className = "datetime";
					}					
				}
				
				cells[0].className = "actor";
				cells[0].innerHTML = '<a href="javascript:removePermissionRow(\'_row:' + rowValue + '\')"><img src="<?=ASSETS_URL_IMAGES?>/icons/remove.png" width="12" height="12"></a> ' + uName;
				cells[1].innerHTML = '<input type="checkbox" name="collectionRead[]" value="' + rowValue + '">';
				cells[2].innerHTML = '<input type="checkbox" name="collectionReadVersions[]" value="' + rowValue + '">';
				cells[3].innerHTML = '<input type="checkbox" name="collectionWrite[]" value="' + rowValue + '">';
				cells[4].innerHTML = '<input type="checkbox" name="collectionApprove[]" value="' + rowValue + '">';
				cells[5].innerHTML = '<input type="checkbox" name="collectionDelete[]" value="' + rowValue + '">';
				cells[6].innerHTML = '<input type="checkbox" name="collectionAdmin[]" value="' + rowValue +'">';
				<? 
				foreach ($ctArray as $ct) { ?>
					cells[7].innerHTML += '<div style="white-space: nowrap; width: auto; float: left; margin-right: 5px; min-width: 90px"><input type="checkbox" name="collectionAddSubCollection[<?=$ct->getCollectionTypeID()?>][]" value="' + rowValue + '">&nbsp;<?=$ct->getCollectionTypeName()?></div>';
				<? } ?>
				cells[7].innerHTML += '<div class="ccm-spacer">&nbsp;</div>';
				cells[8].innerHTML = '<div style="text-align: center; color: #aaa"><?=$saveMsg?></div>';
				cells[9].innerHTML = '<div style="text-align: center; color: #aaa"><?=$saveMsg?></div>';
			}
		}
		
		function ccm_triggerSelectGroup(gID, gName) {
			togglePermissionsGrid('permissions', true);
			// we add a row for the selected group
			rowValue = 'gID:' + gID;
			rowText = gName;
			existingRow = document.getElementById("_row:" + rowValue);
			if (rowValue && (existingRow == null)) {
				tbl = document.getElementById("ccmPermissionsTablePage");	      

				row = tbl.insertRow(-1); // insert at bottom of table. safari, wtf ?                            
				row.id = "_row:" + rowValue;
				
				ccm_setupGridStriping('ccmPermissionsTablePage');
				
				cells = new Array();
				
				target = 10;
				for (i = 0; i < target; i++) {
					cells[i] = row.insertCell(i);
					if (i < 7 && i > 0) {
						cells[i].className = "permissions";
					} else if (i == 7) {
						cells[i].className = "subpage";
					} else {
						cells[i].className = "datetime";
					}					
				}
				
				cells[0].className = "actor";
				cells[0].innerHTML = '<a href="javascript:removePermissionRow(\'_row:' + rowValue + '\',\'' + rowValue + '\',\'' + rowText + '\')"><img src="<?=ASSETS_URL_IMAGES?>/icons/remove.png" width="12" height="12"></a>' + rowText;
				cells[1].innerHTML = '<input type="checkbox" name="collectionRead[]" value="' + rowValue + '">';
				cells[2].innerHTML = '<input type="checkbox" name="collectionReadVersions[]" value="' + rowValue + '">';
				cells[3].innerHTML = '<input type="checkbox" name="collectionWrite[]" value="' + rowValue + '">';
				cells[4].innerHTML = '<input type="checkbox" name="collectionApprove[]" value="' + rowValue + '">';
				cells[5].innerHTML = '<input type="checkbox" name="collectionDelete[]" value="' + rowValue + '">';
				cells[6].innerHTML = '<input type="checkbox" name="collectionAdmin[]" value="' + rowValue +'">';
				<? 
				foreach ($ctArray as $ct) { ?>
					cells[7].innerHTML += '<div style="white-space: nowrap; width: auto; float: left; margin-right: 5px; min-width: 90px"><input type="checkbox" name="collectionAddSubCollection[<?=$ct->getCollectionTypeID()?>][]" value="' + rowValue + '">&nbsp;<?=$ct->getCollectionTypeName()?></div>';
				<? } ?>
				cells[7].innerHTML += '<div class="ccm-spacer">&nbsp;</div>';
				cells[8].innerHTML = '<div style="text-align: center; color: #aaa"><?=$saveMsg?></div>';
				cells[9].innerHTML = '<div style="text-align: center; color: #aaa"><?=$saveMsg?></div>';
				
			}            
		}
		
		function removePermissionRow(rowID, origValue, origText) {
		  oRow = document.getElementById(rowID);
		  oRowInputs = oRow.getElementsByTagName("INPUT");
	    	  for (i = 0; i < oRowInputs.length; i++) {
	    	       oRowInputs[i].checked = false;
	    	  }
	    	  oRow.id = null;
	    	  oRow.style.display = "none";

			  ccm_setupGridStriping('ccmPermissionsTablePage');
			}
			
	</script>
	<? if ($cp->canAdminPage()) { ?>
		<style type="text/css">
			.datetime {display: none}
			.subpage {display: none}
			#ccmPermissionsTablePage .ccm-input-time-wrapper {display: block; margin-left: 14px !important; margin-top: 8px}
			#ccmPermissionsTablePage .ccm-input-date-wrapper {margin-left: 4px;}
			#ccmPermissionsTablePage .ccm-input-date-wrapper input {width: 140px !important}
			td.permissions {text-align: center}
			
		</style>
		
		<div class="ccm-pane-options">
		  <strong><?=t('Set')?></strong>&nbsp;
		   <select id="ccmToggleInheritance" style="width: 130px" name="cInheritPermissionsFrom">
			<? if ($c->getCollectionID() > 1) { ?><option value="PARENT" <? if ($c->getCollectionInheritance() == "PARENT") { ?> selected<? } ?>><?=t('By Area of Site (Hierarchy)')?></option><? } ?>
			<? if ($c->getMasterCollectionID() > 1) { ?><option value="TEMPLATE"  <? if ($c->getCollectionInheritance() == "TEMPLATE") { ?> selected<? } ?>><?=t('By Page Type Defaults (in Dashboard)')?></option><? } ?>
			<option value="OVERRIDE" <? if ($c->getCollectionInheritance() == "OVERRIDE") { ?> selected<? } ?>><?=t('Manually')?></option>
		  </select>
		  
		  &nbsp;&nbsp;&nbsp;

		  <strong><?=t('Currently Viewing')?></strong>&nbsp;
		  <select id="toggleGrid" style="width: 130px" onchange="togglePermissionsGrid(this.value)">
			<option value="permissions" selected><?=t('Page Permissions')?></option>
			<option value="subpage"><?=t('Sub-Page Permissions')?></option>
			<option value="datetime"><?=t('Timed Release Settings')?></option>
		  </select>

		
		<a class="btn ccm-button-right dialog-launch" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/users/search_dialog?mode=choose_multiple&cID=<?=$_REQUEST['cID']?>" dialog-modal="false" dialog-width="90%" dialog-title="<?=t('Add User')?>"  dialog-height="70%"><?=t('Add User')?></a>
		<a class="btn ccm-button-right dialog-launch" style="margin-right: 5px" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/select_group?cID=<?=$_REQUEST['cID']?>" dialog-modal="false" dialog-title="<?=t('Add Group')?>"><?=t('Add Group')?></a>

		</div>


		
		<div class="clearfix"></div>
		
		
		 <?
		  $cpc = $c->getPermissionsCollectionObject();
		  $isManual = ($c->getCollectionInheritance() == "OVERRIDE");
	  		if ($c->getCollectionInheritance() == "PARENT") { ?>
			<strong><?=t('This page inherits its permissions from:');?> <a target="_blank" href="<?=DIR_REL?>/<?=DISPATCHER_FILENAME?>?cID=<?=$cpc->getCollectionID()?>"><?=$cpc->getCollectionName()?></a></strong><br/><br/>
			<? } ?>		
				
            <table id="ccmPermissionsTablePage" width="100%" class="ccm-grid" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <th><div style="width: 200px">&nbsp;</div></th>
              <th class="permissions"><?=t('Read')?></th>
              <th class="permissions"><?=t('Versions')?></th>
              <th class="permissions"><?=t('Write')?></th>
              <th class="permissions"><?=t('Approve')?></th>
              <th class="permissions"><?=t('Delete')?></th>
              <th class="permissions"><?=t('Admin')?></th>
              <th class="subpage"><?=t('User/Group May Add the Following Pages')?>:</th>
              <th class="datetime"><?=t('Start Date/Time')?></th>
              <th class="datetime"><?=t('End Date/Time')?></th>              	
            </tr>
            <? 
            $rowNum = 1;
            foreach ($gArray as $g) { 
                $displayRow = false;
                
                $display = (($g->getGroupID() == GUEST_GROUP_ID || $g->getGroupID() == REGISTERED_GROUP_ID) || $g->canRead() || $g->canWrite() || $g->canAddSubContent() || $g->canApproveCollection() || $g->canDeleteCollection() || ($c->isMasterCollection() && $g->canAddSubCollection())) 
                ? true : false;
                       
                if ($display) { ?>
                    <tr id="_row:gID:<?=$g->getGroupID()?>">
                        <td class="actor" style="width: 1%; white-space: nowrap">
                            <? if ($g->getGroupID() != GUEST_GROUP_ID && $g->getGroupID() != REGISTERED_GROUP_ID) { ?>    
                                <a href="javascript:removePermissionRow('_row:gID:<?=$g->getGroupID()?>','gID:<?=$g->getGroupID()?>', '<?=$g->getGroupName()?>')"><img src="<?=ASSETS_URL_IMAGES?>/icons/remove.png" width="12" height="12"></a>
							<? } ?>
							<?=$g->getGroupName()?>
						</td>
						<td class="permissions"><input type="checkbox" name="collectionRead[]" value="gID:<?=$g->getGroupID()?>"<? if ($g->canRead()) { ?>checked<? } ?>></td>
                        <td class="permissions"><input type="checkbox" name="collectionReadVersions[]" value="gID:<?=$g->getGroupID()?>"<? if ($g->canReadVersions()) { ?>checked<? } ?>></td>

                        <td class="permissions"><input type="checkbox" name="collectionWrite[]" value="gID:<?=$g->getGroupID()?>"<? if ($g->canWrite()) { ?>checked<? } ?>></td>
                        <td class="permissions"><input type="checkbox" name="collectionApprove[]" value="gID:<?=$g->getGroupID()?>"<? if ($g->canApproveCollection()) { ?>checked<? } ?>></td>
                        <td class="permissions"><input type="checkbox" name="collectionDelete[]" value="gID:<?=$g->getGroupID()?>"<? if ($g->canDeleteCollection()) { ?>checked<? } ?>></td>
                   		<td class="permissions"><input type="checkbox" name="collectionAdmin[]" value="gID:<?=$g->getGroupID()?>"<? if ($g->canAdminCollection()) { ?> checked<? } ?>></td>
                   		
                   		<td class="subpage"><? foreach ($ctArray as $ct) { ?><div style="white-space: nowrap; width: auto; float: left; margin-right: 5px; min-width: 90px"><input type="checkbox" name="collectionAddSubCollection[<?=$ct->getCollectionTypeID()?>][]" value="gID:<?=$g->getGroupID()?>"<? if ($ct->canAddSubCollection($g)) { ?> checked<? } ?>>&nbsp;<?=$ct->getCollectionTypeName()?></div><? } ?><div class="ccm-spacer">&nbsp;</div></td>
						<td class="datetime"><?	print $dt->datetime('cgStartDate_gID:' . $g->getGroupID(), $g->getGroupStartDate('user'), true); ?>
						<td class="datetime"><?	print $dt->datetime('cgEndDate_gID:' . $g->getGroupID(), $g->getGroupEndDate('user'), true); ?>
				
                  
                  </tr>
                <? 
                    $rowNum++;
                    } ?>
            <?  }
                        
            foreach ($ulArray as $ui) { 
				?>
               <tr id="_row:uID:<?=$ui->getUserID()?>">
                    <td class="actor">
                        <a href="javascript:removePermissionRow('_row:uID:<?=$ui->getUserID()?>')"><img src="<?=ASSETS_URL_IMAGES?>/icons/remove.png" width="12" height="12"></a>
                        <?=$ui->getUserName()?>                            
                    </td>
                    <td class="permissions"><input type="checkbox" name="collectionRead[]" value="uID:<?=$ui->getUserID()?>"<? if ($ui->canRead()) { ?>checked<? } ?>></td>
                    <td class="permissions"><input type="checkbox" name="collectionReadVersions[]" value="uID:<?=$ui->getUserID()?>"<? if ($ui->canReadVersions()) { ?>checked<? } ?>></td>
                    <td class="permissions"><input type="checkbox" name="collectionWrite[]" value="uID:<?=$ui->getUserID()?>"<? if ($ui->canWrite()) { ?>checked<? } ?>></td>
                    <td class="permissions"><input type="checkbox" name="collectionApprove[]" value="uID:<?=$ui->getUserID()?>"<? if ($ui->canApproveCollection()) { ?>checked<? } ?>></td>
                    <td class="permissions"><input type="checkbox" name="collectionDelete[]" value="uID:<?=$ui->getUserID()?>"<? if ($ui->canDeleteCollection()) { ?>checked<? } ?>></td>
              		<td class="permissions"><input type="checkbox" name="collectionAdmin[]" value="uID:<?=$ui->getUserID()?>"<? if ($ui->canAdminCollection()) { ?> checked<? } ?>></td>
              		<td class="subpage">
					<? foreach ($ctArray as $ct) { ?>
						<div style="white-space: nowrap; width: auto; float: left; margin-right: 5px; min-width: 90px"><input type="checkbox" name="collectionAddSubCollection[<?=$ct->getCollectionTypeID()?>][]" value="uID:<?=$ui->getUserID()?>"<? if ($ct->canAddSubCollection($ui)) { ?> checked<? } ?>>&nbsp;<?=$ct->getCollectionTypeName()?></div><? } ?>
					<div class="ccm-spacer">&nbsp;</div>
					</td>
              		<td class="datetime"><input type="text" id="cgStartDate_uID:<?=$ui->getUserID()?>" name="cgStartDate_uID:<?=$ui->getUserID()?>" value="<?=$ui->getUserStartDate()?>" style="width: 130px">&nbsp;<input type="button" name="" value="date" onclick="popUpCalendar(this, document.getElementById('cgStartDate_uID:<?=$ui->getUserID()?>'), 'yyyy-mm-dd')"></td>
					<td class="datetime"><input type="text" id="cgEndDate_uID:<?=$ui->getUserID()?>" name="cgEndDate_uID:<?=$ui->getUserID()?>" value="<?=$ui->getUserEndDate()?>" style="width: 130px">&nbsp;<input type="button" name="" value="date" onclick="popUpCalendar(this, document.getElementById('cgEndDate_uID:<?=$ui->getUserID()?>'), 'yyyy-mm-dd')"></td>

                </tr>
            <? 
                $rowNum++;
                } ?>
            </table>		
            <br/>
            <? if (!$c->isMasterCollection()) { ?>
				<b><?=t('Sub-pages added beneath this page')?></b>: 
				<select id="templatePermissionsSelect" name="cOverrideTemplatePermissions">
					<option value="0"<? if (!$c->overrideTemplatePermissions()) { ?>selected<? } ?>><?=t('Inherit page type default permissions.')?></option>
					<option value="1"<? if ($c->overrideTemplatePermissions()) { ?>selected<? } ?>><?=t('Inherit the permissions of this page.')?></option>
				</select>
				<br><br>
				<? } ?>
				
			<div class="dialog-buttons">
				<a href="javascript:void(0)" onclick="jQuery.fn.dialog.closeTop();" class="ccm-button-left btn"><?=t('Cancel')?></a>
				<a href="javascript:void(0)" class="btn primary ccm-button-right" onclick="$('form[name=ccmPermissionsForm]').submit()"><?=t('Save')?></a>
			</div>	
			<input type="hidden" name="update_permissions" value="1" class="accept">
			<input type="hidden" name="processCollection" value="1">

<script type="text/javascript">
ccm_deactivatePermissionsTable = function() {
	$("#ccmPermissionsTablePage input, #ccmPermissionsTablePage select").each(function(i) {
		$(this).get(0).disabled = true;
	});
	$("#ccm-page-permissions-select-user-group").hide();
}

ccm_activatePermissionsTable = function() {
	$("#ccmPermissionsTablePage input, #ccmPermissionsTablePage select").each(function(i) {
		$(this).get(0).disabled = false;
	});
	$("input.ccm-activate-date-time").each(function() {
		var thisID = $(this).attr('ccm-date-time-id');
		if (!$(this).get(0).checked) {
			$("#" + thisID + "_dw input").each(function() {
				$(this).get(0).disabled = true;
			});
			$("#" + thisID + "_tw select").each(function() {
				$(this).get(0).disabled = true;
			});		}
	});
	$("#ccm-page-permissions-select-user-group").show();
}
$(function() {	
	<? if (!$isManual) { ?>
		ccm_deactivatePermissionsTable();
	<? } ?>
	$("#ccmPermissionsForm").ajaxForm({
		type: 'POST',
		iframe: true,
		beforeSubmit: function() {
			jQuery.fn.dialog.showLoader();
		},
		success: function(r) {
			var r = eval('(' + r + ')');
			<? if (isset($isComposer) && $isComposer) { ?>
				jQuery.fn.dialog.hideLoader();						
				jQuery.fn.dialog.closeTop();
			<? } else { ?>
				if (r != null && r.rel == 'SITEMAP') {
					jQuery.fn.dialog.hideLoader();
					jQuery.fn.dialog.closeTop();
					ccmSitemapHighlightPageLabel(r.cID);
				} else {
					jQuery.fn.dialog.hideLoader();
					jQuery.fn.dialog.closeTop();
				}
			<? } ?>
			ccmAlert.hud(ccmi18n_sitemap.setPagePermissionsMsg, 2000, 'success', ccmi18n_sitemap.setPagePermissions);
		}
	});
	$("#ccmToggleInheritance").change(function() {
		if ($(this).val() == 'OVERRIDE') {
			ccm_activatePermissionsTable();
		} else {
			ccm_deactivatePermissionsTable();
		}
	});
	ccm_setupGridStriping('ccmPermissionsTablePage'); 
});
</script>

	<? } ?>
</form>
<div class="ccm-spacer">&nbsp;</div>
</div>