<?php  
	$applyMSG = t('Apply these changes to all blocks aliased to this block? Note: This may take some time.');
	
	defined('C5_EXECUTE') or die(_("Access Denied."));
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
			<?php  if ($numChildren) { ?>
			if (confirm("<?php echo $applyMSG?>")) {
				document.forms['ccmBlockPermissionForm'].action = document.forms['ccmBlockPermissionForm'].action + "&applyToAll=1";
			}
			<?php  } ?>
			document.forms['ccmBlockPermissionForm'].submit();
		}
		
		<?php  if ($numChildren) { ?>
		function applyToAll() {
			if (confirm("<?php echo $applyMSG?>")) {
				document.forms['ccmBlockPermissionForm'].action = document.forms['ccmBlockPermissionForm'].action + "&applyToAll=1";
				$('#ccmBlockPermissionForm').submit();
			} else {
				$('#ccmBlockPermissionForm').submit();
			}
		}
		<?php  } ?>
		
		function ccm_triggerSelectUser(uID, uName) {
		  rowValue = "uID:" + uID;
		  existingRow = document.getElementById("_row:" + rowValue);		  
		  if (!existingRow) {
		      tbl = document.getElementById("ccmPermissionsTable");	      
              row = tbl.insertRow(-1); // insert at bottom of table. safari, wtf ?                            
              row.id = "_row:" + rowValue;

			ccm_setupGridStriping('ccmPermissionsTable');
              
              cells = new Array();
				for (i = 0; i < 4; i++) {
					cells[i] = row.insertCell(i);
				}
				
				cells[0].className = "actor";
				cells[0].innerHTML = '<a href="javascript:removePermissionRow(\'_row:' + rowValue + '\')"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/remove.png" width="12" height="12"></a>' + uName;
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
               
            tbl = document.getElementById("ccmPermissionsTable");	      
            row = tbl.insertRow(-1); // insert at bottom of table. safari, wtf ?                            
            row.id = "_row:" + rowValue;

			ccm_setupGridStriping('ccmPermissionsTable');

              
              cells = new Array();
				for (i = 0; i < 4; i++) {
					cells[i] = row.insertCell(i);
				}
              
              	cells[0].className = "actor";
              	cells[0].innerHTML = '<a href="javascript:removePermissionRow(\'_row:' + rowValue + '\',\'' + rowValue + '\',\'' + rowText + '\')"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/remove.png" width="12" height="12"></a>' + rowText;
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

			ccm_setupGridStriping('ccmPermissionsTable');
		}
	
	$(function() {
		ccm_setupGridStriping('ccmPermissionsTable');
	});
	</script>
	
<?php  global $c;?>
<h1><?php echo t('Block Permissions')?></h1>
<form method="post" name="ccmBlockPermissionForm" id="ccmBlockPermissionForm" action="<?php echo $gl->getGroupUpdateAction($b)?>&rcID=<?php echo intval($rcID)?>">
	<span class="ccm-important">
	<?php  if (!$b->overrideAreaPermissions()) { ?>
		<?php echo t('Permissions for this block are currently dependent on the area containing this block. If you override those permissions here, they will not match those of the area.')?><br/><br/>
	<?php  } else { ?>
		<?php echo t("Permissions for this block currently override those of the parent area. To revert to the area's permissions, click the button below.")?><br/><br/>
	<?php  } ?>	
	</span>
		<div class="ccm-buttons" style="margin-bottom: 10px"> 
		<a href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/user_group_selector.php?cID=<?php echo $_REQUEST['cID']?>" dialog-width="90%" dialog-title="<?php echo t('Add User/Group')?>"  dialog-height="70%" dialog-modal="false" class="dialog-launch ccm-button-right"><span><em class="ccm-button-add"><?php echo t('Add User/Group')?></em></span></a>
		</div>

		<div class="ccm-spacer">&nbsp;</div>
<br/>
            <table id="ccmPermissionsTable" border="0" cellspacing="0" cellpadding="0" class="ccm-grid" style="width: 100%">
            <tr>
               <th style="width: 100%">&nbsp;</th>
              <th><?php echo t('Read')?></th>
              <th><?php echo t('Write')?></th>
              <th><?php echo t('Delete')?></th>
                     
            </tr>
            <?php  
            $rowNum = 1;
            foreach ($gArray as $g) { 
                $displayRow = false;
                $display = (($g->getGroupID() == GUEST_GROUP_ID || $g->getGroupID() == REGISTERED_GROUP_ID) || $g->canRead() || $g->canWrite() || $g->canDeleteBlock()) 
                ? true : false;
                       
                if ($display) { ?>
                    <tr class="no-bg" id="_row:gID:<?php echo $g->getGroupID()?>">
                        <td class="actor">
                            <?php  if ($g->getGroupID() != GUEST_GROUP_ID && $g->getGroupID() != REGISTERED_GROUP_ID) { ?>    
                                <a href="javascript:removePermissionRow('_row:gID:<?php echo $g->getGroupID()?>','gID:<?php echo $g->getGroupID()?>', '<?php echo $g->getGroupName()?>')"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/remove.png" width="12" height="12"></a>
                            <?php  } ?>
                            <?php echo $g->getGroupName()?>                            
                        </td>
                        <td><div style="text-align: center"><input type="checkbox" name="blockRead[]" value="gID:<?php echo $g->getGroupID()?>"<?php  if ($g->canRead()) { ?> checked<?php  } ?>></div></td>
                        <td><div style="text-align: center"><input type="checkbox" name="blockWrite[]" value="gID:<?php echo $g->getGroupID()?>"<?php  if ($g->canWrite()) { ?> checked<?php  } ?>></div></td>
						<td><div style="text-align: center"><input type="checkbox" name="blockDelete[]" value="gID:<?php echo $g->getGroupID()?>"<?php  if ($g->canDeleteBlock()) { ?> checked<?php  } ?>></div></td>
                    </tr>
                <?php  
                    $rowNum++;
                    } ?>
            <?php   }
                        
            foreach ($ulArray as $ui) { ?>
               <tr class="no-bg" id="_row:uID:<?php echo $ui->getUserID()?>">
                    <td class="actor">
                        <a href="javascript:removePermissionRow('_row:uID:<?php echo $ui->getUserID()?>')"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/remove.png" width="12" height="12"></a>
                        <?php echo $ui->getUserName()?>                            
                    </td>
                    <td><div style="text-align: center"><input type="checkbox" name="blockRead[]" value="uID:<?php echo $ui->getUserID()?>"<?php  if ($ui->canRead()) { ?> checked<?php  } ?>></div></td>
                    <td><div style="text-align: center"><input type="checkbox" name="blockWrite[]" value="uID:<?php echo $ui->getUserID()?>"<?php  if ($ui->canWrite()) { ?> checked<?php  } ?>></div></td>
                    <td><div style="text-align: center"><input type="checkbox" name="blockDelete[]" value="uID:<?php echo $ui->getUserID()?>"<?php  if ($ui->canDeleteBlock()) { ?> checked<?php  } ?>></div></td>
                </tr>
            <?php  
                $rowNum++;
                } ?>
        </table>	
		
		<br>
		<?php  // this value is always 1, because if we ever submit this form using "update", it's assumed we want the permissions
		// we're submitting to override the page's permissions ?>
		<input type="hidden" name="cbOverrideAreaPermissions" value="1" id="cbOverrideAreaPermissions">

		<div class="ccm-buttons">
		<a href="javascript:void(0)" onclick="<?php  if ($numChildren) { ?>applyToAll();<?php  } else { ?>$('#ccmBlockPermissionForm').submit()<?php  } ?>" class="ccm-button-right accept"><span><?php echo t('Update')?></span></a>
		<a href="javascript:void(0)" class="ccm-button-left cancel ccm-dialog-close"><span><em class="ccm-button-close"><?php echo t('Cancel')?></em></span></a>
		</div>
<?php 
$valt = Loader::helper('validation/token');
$valt->output();
?>
</form>

<script type="text/javascript">
$(function() {
	$('#ccmBlockPermissionForm').each(function() {
		ccm_setupBlockForm($(this), '<?php echo $b->getBlockID()?>', 'edit');
	});
});
</script>