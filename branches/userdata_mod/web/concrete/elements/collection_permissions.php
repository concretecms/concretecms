<?
Loader::model('collection_types');
$dh = Loader::helper('date');
$dt = Loader::helper('form/date_time');

if ($cp->canAdminPage()) {
$gl = new GroupList($c);
$gArray = $gl->getGroupList();
$ul = new UserInfoList($c);
$ulArray = $ul->getUserInfoList();
$ctArray = CollectionType::getList($c);
}
?>
<div class="ccm-pane-controls">

<form method="post" name="ccmPermissionsForm" action="<?=$c->getCollectionAction()?>">
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
				tbl = document.getElementById("ccmPermissionsTable");

				row = tbl.insertRow(-1); // insert at bottom of table. safari, wtf ?                            
				row.id = "_row:" + rowValue;
				
				ccm_setupGridStriping('ccmPermissionsTable');
				
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
				cells[0].innerHTML = '<a href="javascript:removePermissionRow(\'_row:' + rowValue + '\')"><img src="<?=ASSETS_URL_IMAGES?>/icons/remove.png" width="12" height="12"></a>' + uName;
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
				cells[8].innerHTML = '<div style="text-align: center; color: #aaa">Save permissions first.</div>';
				cells[9].innerHTML = '<div style="text-align: center; color: #aaa">Save permissions first.</div>';
			}
		}
		
		function ccm_addGroup(gID, gName) {
			togglePermissionsGrid('permissions', true);
			// we add a row for the selected group
			rowValue = 'gID:' + gID;
			rowText = gName;
			existingRow = document.getElementById("_row:" + rowValue);
			if (rowValue && (existingRow == null)) {
				tbl = document.getElementById("ccmPermissionsTable");	      

				row = tbl.insertRow(-1); // insert at bottom of table. safari, wtf ?                            
				row.id = "_row:" + rowValue;
				
				ccm_setupGridStriping('ccmPermissionsTable');
				
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
				cells[8].innerHTML = '<div style="text-align: center; color: #aaa">Save permissions first.</div>';
				cells[9].innerHTML = '<div style="text-align: center; color: #aaa">Save permissions first.</div>';
				
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

			  ccm_setupGridStriping('ccmPermissionsTable');
			}
			
	</script>
	<? if ($cp->canAdminPage()) { ?>
		<style type="text/css">
			.datetime {display: none}
			.subpage {display: none}
			#ccmPermissionsTable .ccm-input-time-wrapper {display: block; margin-left: 14px}
			td.permissions {text-align: center}
			
			#ccmPermissionsTable .ccm-input-date-wrapper input.ccm-input-date {width: 120px;}
			
		</style>
		
		<h1 style="margin-bottom: 0px">Page Permissions</h1>

		<div class="ccm-buttons" style="width: 140px; float: right"> 
		<a href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/user_group_selector.php?cID=<?=$_REQUEST['cID']?>" dialog-modal="false" dialog-width="600" dialog-title="Choose User/Group"  dialog-height="400" class="dialog-launch ccm-button-right"><span><em class="ccm-button-add">Add User/Group</em></span></a>
		</div>		

		<div style="float: left; width: 450px; padding-top: 15px">
		  <strong>Set By:</strong>
		   <select id="ccmToggleInheritance" style="width: 130px" name="cInheritPermissionsFrom">
			<? if ($c->getCollectionID() > 1) { ?><option value="PARENT" <? if ($c->getCollectionInheritance() == "PARENT") { ?> selected<? } ?>>Area of Site (Hierarchy)</option><? } ?>
			<? if ($c->getMasterCollectionID() > 1) { ?><option value="TEMPLATE"  <? if ($c->getCollectionInheritance() == "TEMPLATE") { ?> selected<? } ?>>Page Type (Master Collection)</option><? } ?>
			<option value="OVERRIDE" <? if ($c->getCollectionInheritance() == "OVERRIDE") { ?> selected<? } ?>>Manual Override</option>
		  </select>
		  
		  &nbsp;&nbsp;&nbsp;

		  <strong>Currently Viewing</strong>
		  <select id="toggleGrid" style="width: 130px" onchange="togglePermissionsGrid(this.value)">
			<option value="permissions" selected>Page Permissions</option>
			<option value="subpage">Sub-Page Permissions</option>
			<option value="datetime">Timed Release Settings</option>
		  </select>
		</div>
		
		<div class="ccm-spacer" style="margin-bottom: 8px">&nbsp;</div>
		
		
		 <?
		  $cpc = $c->getPermissionsCollectionObject();
		  $isManual = ($c->getCollectionInheritance() == "OVERRIDE");
	  		if ($c->getCollectionInheritance() == "PARENT") { ?>
			<strong>This page inherits its permissions from: <a target="_blank" href="<?=DIR_REL?>/<?=DISPATCHER_FILENAME?>?cID=<?=$cpc->getCollectionID()?>"><?=$cpc->getCollectionName()?></a></strong><br/><br/>
			<? } ?>		
				
            <table id="ccmPermissionsTable" width="100%" class="ccm-grid" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <th><div style="width: 200px">&nbsp;</div></th>
              <th class="permissions">Read</th>
              <th class="permissions">Versions</th>
              <th class="permissions">Write</th>
              <th class="permissions">Approve</th>
              <th class="permissions">Delete</th>
              <th class="permissions">Admin</th>
              <th class="subpage">User/Group May Add the Following Pages:</th>
              <th class="datetime">Start Date/Time</th>
              <th class="datetime">End Date/Time</th>              	
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
						<td class="datetime"><?	print $dt->datetime('cgStartDate_gID:' . $g->getGroupID(), $g->getGroupStartDate(), true); ?>
						<td class="datetime"><?	print $dt->datetime('cgEndDate_gID:' . $g->getGroupID(), $g->getGroupEndDate(), true); ?>
				
                  
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
				<b>Sub-pages added to this page</b>: 
				<select id="templatePermissionsSelect" name="cOverrideTemplatePermissions">
					<option value="0"<? if (!$c->overrideTemplatePermissions()) { ?>selected<? } ?>>Inherit master collection permissions.</option>
					<option value="1"<? if ($c->overrideTemplatePermissions()) { ?>selected<? } ?>>Inherit the permissions of this page.</option>
				</select>
				<br><br>
				<? } ?>
				
			<div class="ccm-buttons">
<!--				<a href="javascript:void(0)" onclick="ccm_hidePane()" class="ccm-button-left cancel"><span><em class="ccm-button-close">Cancel</em></span></a>//-->
				<a href="javascript:void(0)" onclick="ccm_submit()" class="ccm-button-right accept"><span>Save</span></a>
			</div>	
			<input type="hidden" name="update_permissions" value="1" class="accept">
			<input type="hidden" name="processCollection" value="1">

<script type="text/javascript">
ccm_submit = function() {
	//ccm_showTopbarLoader();
	$('form[name=ccmPermissionsForm]').get(0).submit();
}

ccm_deactivatePermissionsTable = function() {
	$("#ccmPermissionsTable input, #ccmPermissionsTable select").each(function(i) {
		$(this).get(0).disabled = true;
	});
}

ccm_activatePermissionsTable = function() {
	$("#ccmPermissionsTable input, #ccmPermissionsTable select").each(function(i) {
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
}
$(function() {	
	<? if (!$isManual) { ?>
		ccm_deactivatePermissionsTable();
	<? } ?>
	$("#ccmToggleInheritance").change(function() {
		if ($(this).val() == 'OVERRIDE') {
			ccm_activatePermissionsTable();
		} else {
			ccm_deactivatePermissionsTable();
		}
	});
	ccm_setupGridStriping('ccmPermissionsTable'); 
});
</script>

	<? } ?>
</form>
<div class="ccm-spacer">&nbsp;</div>
</div>