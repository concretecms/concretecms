<? 
	$applyMSG = t('Apply these changes to all blocks aliased to this block? Note: This may take some time.');
	
	defined('C5_EXECUTE') or die("Access Denied.");
	global $c;
	global $a;
	
	if ($b->overrideAreaPermissions()) {
		$gl = new GroupList($b);
		$ul = new UserInfoList($b);
	} else if ($a->overrideCollectionPermissions()) {
		$gl = new GroupList($a);
		$ul = new UserInfoList($a);
	} else {
		$gl = new GroupList($c);
		$ul = new UserInfoList($c);
	}
	
	$gArray = $gl->getGroupList();
	$ulArray = $ul->getUserInfoList();
	// $p is the permissions object for this black
	$isAlias = $b->isAlias();
	$numChildren = (!$isAlias) ? $b->getNumChildren() : 0; ?>
	<script type="text/javascript">
		function revertToPagePermissions() {
			ff = document.getElementById('cbOverrideAreaPermissions');
			ff.value = '0';
			<? if ($numChildren) { ?>
			if (confirm("<?=$applyMSG?>")) {
				document.forms['ccmBlockPermissionForm'].action = document.forms['ccmBlockPermissionForm'].action + "&applyToAll=1";
			}
			<? } ?>
			$('#ccmBlockPermissionForm').submit();
		}
		
		<? if ($numChildren) { ?>
		function applyToAll() {
			if (confirm("<?=$applyMSG?>")) {
				document.forms['ccmBlockPermissionForm'].action = document.forms['ccmBlockPermissionForm'].action + "&applyToAll=1";
				$('#ccmBlockPermissionForm').submit();
			} else {
				$('#ccmBlockPermissionForm').submit();
			}
		}
		<? } ?>
		
		function ccm_triggerSelectUser(uID, uName) {
		  rowValue = "uID:" + uID;
		  existingRow = document.getElementById("_row:" + rowValue);		  
		  if (!existingRow) {
		  	  tbl = document.getElementById("ccmPermissionsTableBlock");	      
              row = tbl.insertRow(-1); // insert at bottom of table. safari, wtf ?                            
              row.id = "_row:" + rowValue;

			ccm_setupGridStriping('ccmPermissionsTableBlock');
              
              cells = new Array();
				for (i = 0; i < 4; i++) {
					cells[i] = row.insertCell(i);
				}
				
				cells[0].className = "actor";
				cells[0].innerHTML = '<a href="javascript:removePermissionRow(\'_row:' + rowValue + '\')"><img src="<?=ASSETS_URL_IMAGES?>/icons/remove.png" width="12" height="12"></a> ' + uName;
				cells[1].innerHTML = '<div style="text-align: center"><input type="checkbox" name="blockRead[]" value="' + rowValue + '"></div>';
				cells[2].innerHTML = '<div style="text-align: center"><input type="checkbox" name="blockWrite[]" value="' + rowValue + '"></div>';
				cells[3].innerHTML = '<div style="text-align: center"><input type="checkbox" name="blockDelete[]" value="' + rowValue + '"></div>';
             
            }
		}
		
		function ccm_triggerSelectGroup(gID, gName) {
	      // we add a row for the selected group
	      rowValue = "gID:" + gID;
	      rowText = gName;
          existingRow = document.getElementById("_row:" + rowValue);
          if (!existingRow) {
               
            tbl = document.getElementById("ccmPermissionsTableBlock");	      
            row = tbl.insertRow(-1); // insert at bottom of table. safari, wtf ?                            
            row.id = "_row:" + rowValue;

			ccm_setupGridStriping('ccmPermissionsTableBlock');

              
              cells = new Array();
				for (i = 0; i < 4; i++) {
					cells[i] = row.insertCell(i);
				}
              
              	cells[0].className = "actor";
              	cells[0].innerHTML = '<a href="javascript:removePermissionRow(\'_row:' + rowValue + '\',\'' + rowValue + '\',\'' + rowText + '\')"><img src="<?=ASSETS_URL_IMAGES?>/icons/remove.png" width="12" height="12"></a> ' + rowText;
				cells[1].innerHTML = '<div style="text-align: center"><input type="checkbox" name="blockRead[]" value="' + rowValue + '"></div>';
				cells[2].innerHTML = '<div style="text-align: center"><input type="checkbox" name="blockWrite[]" value="' + rowValue + '"></div>';
				cells[3].innerHTML = '<div style="text-align: center"><input type="checkbox" name="blockDelete[]" value="' + rowValue + '"></div>';
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

			ccm_setupGridStriping('ccmPermissionsTableBlock');
		}
	
	$(function() {
		ccm_setupGridStriping('ccmPermissionsTableBlock');
	});
	</script>
	
<? global $c;?>
<div class="ccm-ui">
<form method="post" name="ccmBlockPermissionForm" id="ccmBlockPermissionForm" action="<?=$gl->getGroupUpdateAction($b)?>">
	<br/>
	<div class="block-message alert-message notice">
	<? if (!$b->overrideAreaPermissions()) { ?>
		<?=t('Permissions for this block are currently dependent on the area containing this block. If you override those permissions here, they will not match those of the area.')?><br/><br/>
	<? } else { ?>
		<?=t("Permissions for this block currently override those of the parent area. To revert to the area's permissions, click the button below.")?><br/><br/>
	<? } ?>	
	</div>
		<div class="ccm-buttons" style="margin-bottom: 10px"> 
		<?php  if ($b->overrideAreaPermissions()) { ?>
			<a href="javascript:void(0)" onclick="revertToPagePermissions()" class="ccm-button-left cancel btn"><span><?php echo t('Revert to Area Permissions')?></span></a>
		<?php  } ?>
		<a href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/user_group_selector.php?cID=<?=$_REQUEST['cID']?>" dialog-width="90%" dialog-title="<?=t('Add User/Group')?>"  dialog-height="70%" dialog-modal="false" class="dialog-launch btn ccm-button-right"><?=t('Add User/Group')?></a>
		</div>

		<div class="ccm-spacer">&nbsp;</div>
<br/>
            <table id="ccmPermissionsTableBlock" border="0" cellspacing="0" cellpadding="0" class="ccm-grid" style="width: 100%">
            <tr>
               <th style="width: 100%">&nbsp;</th>
              <th><?=t('Read')?></th>
              <th><?=t('Write')?></th>
              <th><?=t('Delete')?></th>
                     
            </tr>
            <? 
            $rowNum = 1;
            foreach ($gArray as $g) { 
                $displayRow = false;
                $display = (($g->getGroupID() == GUEST_GROUP_ID || $g->getGroupID() == REGISTERED_GROUP_ID) || $g->canRead() || $g->canWrite() || $g->canDeleteBlock()) 
                ? true : false;
                       
                if ($display) { ?>
                    <tr class="no-bg" id="_row:gID:<?=$g->getGroupID()?>">
                        <td class="actor">
                            <? if ($g->getGroupID() != GUEST_GROUP_ID && $g->getGroupID() != REGISTERED_GROUP_ID) { ?>    
                                <a href="javascript:removePermissionRow('_row:gID:<?=$g->getGroupID()?>','gID:<?=$g->getGroupID()?>', '<?=$g->getGroupName()?>')"><img src="<?=ASSETS_URL_IMAGES?>/icons/remove.png" width="12" height="12"></a>
                            <? } ?>
                            <?=$g->getGroupName()?>                            
                        </td>
                        <td><div style="text-align: center"><input type="checkbox" name="blockRead[]" value="gID:<?=$g->getGroupID()?>"<? if ($g->canRead()) { ?> checked<? } ?>></div></td>
                        <td><div style="text-align: center"><input type="checkbox" name="blockWrite[]" value="gID:<?=$g->getGroupID()?>"<? if ($g->canWrite()) { ?> checked<? } ?>></div></td>
						<td><div style="text-align: center"><input type="checkbox" name="blockDelete[]" value="gID:<?=$g->getGroupID()?>"<? if ($g->canDeleteBlock()) { ?> checked<? } ?>></div></td>
                    </tr>
                <? 
                    $rowNum++;
                    } ?>
            <?  }
                        
            foreach ($ulArray as $ui) { ?>
               <tr class="no-bg" id="_row:uID:<?=$ui->getUserID()?>">
                    <td class="actor">
                        <a href="javascript:removePermissionRow('_row:uID:<?=$ui->getUserID()?>')"><img src="<?=ASSETS_URL_IMAGES?>/icons/remove.png" width="12" height="12"></a>
                        <?=$ui->getUserName()?>                            
                    </td>
                    <td><div style="text-align: center"><input type="checkbox" name="blockRead[]" value="uID:<?=$ui->getUserID()?>"<? if ($ui->canRead()) { ?> checked<? } ?>></div></td>
                    <td><div style="text-align: center"><input type="checkbox" name="blockWrite[]" value="uID:<?=$ui->getUserID()?>"<? if ($ui->canWrite()) { ?> checked<? } ?>></div></td>
                    <td><div style="text-align: center"><input type="checkbox" name="blockDelete[]" value="uID:<?=$ui->getUserID()?>"<? if ($ui->canDeleteBlock()) { ?> checked<? } ?>></div></td>
                </tr>
            <? 
                $rowNum++;
                } ?>
        </table>	
		
		<br>
		<? // this value is always 1, because if we ever submit this form using "update", it's assumed we want the permissions
		// we're submitting to override the page's permissions ?>
		<input type="hidden" name="cbOverrideAreaPermissions" value="1" id="cbOverrideAreaPermissions">

		<div class="dialog-buttons">
		<a href="javascript:void(0)" onclick="<? if ($numChildren) { ?>applyToAll();<? } else { ?>$('#ccmBlockPermissionForm').submit()<? } ?>" class="ccm-button-right accept btn primary"><?=t('Update')?></a>
		<a href="javascript:void(0)" class="ccm-button-left ccm-dialog-close btn"><?=t('Cancel')?></a>
		</div>
<?
$valt = Loader::helper('validation/token');
$valt->output();
?>
</form>
</div>

<script type="text/javascript">
$(function() {
	$('#ccmBlockPermissionForm').each(function() {
		ccm_setupBlockForm($(this), '<?=$b->getBlockID()?>', 'edit');
	});
});
</script>