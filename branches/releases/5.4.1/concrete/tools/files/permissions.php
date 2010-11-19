<?php 
defined('C5_EXECUTE') or die("Access Denied.");
$u = new User();
$form = Loader::helper('form');
$searchInstance = $_REQUEST['searchInstance'];
$ih = Loader::helper('concrete/interface'); 
$f = File::getByID($_REQUEST['fID']);
$cp = new Permissions($f);
if (!$cp->canAdmin()) {
	die(_("Access Denied."));
}
$form = Loader::helper('form');

if ($_POST['task'] == 'set_password') {
	$f->setPassword($_POST['fPassword']);
	exit;
}


Loader::model('file_storage_location');
if ($_POST['task'] == 'set_location') {
	if ($_POST['fslID'] == 0) {
		$f->setStorageLocation(0);
	} else {
		$fsl = FileStorageLocation::getByID($_POST['fslID']);
		if (is_object($fsl)) {
			$f->setStorageLocation($fsl);
		}
	}
	exit;
}

if ($_POST['task'] == 'set_advanced_permissions') { 
	if ($_POST['fRevertToSetPermissions'] == '1') {
		$f->resetPermissions(0);

	} else {
		
		$f->resetPermissions(1);	
		foreach($_POST['selectedEntity'] as $e) {
			if ($e != '') {
				$id = substr($e, 4);
				if (strpos($e, 'uID') === 0) {
					$obj = UserInfo::getByID($id);
				} else {
					$obj = Group::getByID($id);					
				}
			
				$canRead = $_POST['canRead_' . $e];
				$canWrite = $_POST['canWrite_' . $e];
				$canAdmin = $_POST['canAdmin_' . $e];
				$canSearch = $_POST['canSearch_' . $e];
				
				$f->setPermissions($obj, $canRead, $canSearch, $canWrite, $canAdmin);
			}
		}	
	
	}
	exit;
}
?>

<ul class="ccm-dialog-tabs" id="ccm-file-permissions-tabs">
	<?php  if (PERMISSIONS_MODEL != 'simple') { ?>
		<li class="ccm-nav-active"><a href="javascript:void(0)" id="ccm-file-permissions-advanced"><?php echo t('Permissions')?></a></li>
	<?php  } ?>
	<li <?php  if (PERMISSIONS_MODEL == 'simple') { ?> class="ccm-nav-active" <?php  } ?>><a href="javascript:void(0)" id="ccm-file-password"><?php echo t('Protect with Password')?></a></li>
	<li><a href="javascript:void(0)" id="ccm-file-storage"><?php echo t('Storage Location')?></a></li>
</ul>

<?php  if (PERMISSIONS_MODEL != 'simple') { ?>

<div id="ccm-file-permissions-advanced-tab">

<br/>

<h2><?php echo t('File Permissions')?></h2>

<form method="post" id="ccm-<?php echo $searchInstance?>-permissions-form" action="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/files/permissions/">
<?php echo $form->hidden('task', 'set_advanced_permissions')?>
<?php echo $form->hidden('fID', $f->getFileID())?>

<a style="margin-left: 20px" href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/user_group_selector?include_core_groups=1" id="ug-selector" dialog-modal="false" dialog-width="90%" dialog-title="<?php echo t('Choose User/Group')?>"  dialog-height="70%" class="ccm-button-right dialog-launch"><span><em><?php echo t('Add Group or User')?></em></span></a>

<div class="ccm-important">
<?php  if (!$f->overrideFileSetPermissions()) { ?>
	<?php echo t('Permissions for this file are currently dependent on set and global settings. If you override those permissions here, they will not match those of the file\'s sets.')?><br/><br/>
<?php  } else { ?>
	<?php echo t("Permissions for this file currently override file set and global settings. To revert these permissions, click the button below.")?><br/><br/>
<?php  } ?>	
</div>

<?php  if ($f->overrideFileSetPermissions()) {
	$gl = new GroupList($f);
	$ul = new UserInfoList($f);
} else {
	$sets = array();
	$fsets = $f->getFileSets();
	foreach($fsets as $fs) {
		if ($fs->overrideGlobalPermissions()) {
			$sets[] = $fs;
		}
	}
	
	if (count($sets) > 0) {
		$fsl = new FileSetList();
		foreach($sets as $fs) {
			$fsl->sets[] = $fs;
		}
		$gl = new GroupList($fsl);
		$ul = new UserInfoList($fsl);		
	} else {
		$fs = FileSet::getGlobal();
		$gl = new GroupList($fs);
		$ul = new UserInfoList($fs);
	}
}

$gArray = $gl->getGroupList();
$uArray = $ul->getUserInfoList();
?>
	<table id="ccmPermissionsTable" border="0" cellspacing="0" cellpadding="0" class="ccm-grid" style="width: 100%">
	<tr>
	   <th style="width: 100%">&nbsp;</th>
	  <th><?php echo t('Read')?></th>
	  <th><?php echo t('Search')?></th>
	  <th><?php echo t('Write')?></th>
	  <th><?php echo t('Admin')?></th>
			 
	</tr>
	<?php  
	$rowNum = 1;
	foreach ($gArray as $g) { 
		$displayRow = false;
		$selectedEntity = 'gID_' . $g->getGroupID();
		$display = (($g->getGroupID() == GUEST_GROUP_ID || $g->getGroupID() == REGISTERED_GROUP_ID) || $g->canRead() || $g->canWrite() || $g->canAdmin()) 
		? true : false;
			   
		if ($display) { ?>
			<tr class="no-bg" id="_row:gID:<?php echo $g->getGroupID()?>">
				<td class="actor">
					<input type="hidden" name="selectedEntity[]" value="gID_<?php echo $g->getGroupID()?>" />
					<?php  if ($g->getGroupID() != GUEST_GROUP_ID && $g->getGroupID() != REGISTERED_GROUP_ID) { ?>    
						<a href="javascript:removePermissionRow('_row:gID:<?php echo $g->getGroupID()?>','gID:<?php echo $g->getGroupID()?>', '<?php echo $g->getGroupName()?>')"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/remove.png" width="12" height="12"></a>
					<?php  } ?>
					<?php echo $g->getGroupName()?>                            
				</td>
				<td><div style="text-align: center"><input type="checkbox" name="canRead_<?php echo $selectedEntity?>" value="<?php echo FilePermissions::PTYPE_ALL?>"<?php  if ($g->canRead()) { ?> checked<?php  } ?>></div></td>
				<td><div style="text-align: center"><input type="checkbox" name="canSearch_<?php echo $selectedEntity?>" value="<?php echo FilePermissions::PTYPE_ALL?>"<?php  if ($g->canSearchFiles()) { ?> checked<?php  } ?>></div></td>
				<td><div style="text-align: center"><input type="checkbox" name="canWrite_<?php echo $selectedEntity?>" value="<?php echo FilePermissions::PTYPE_ALL?>"<?php  if ($g->canWrite()) { ?> checked<?php  } ?>></div></td>
				<td><div style="text-align: center"><input type="checkbox" name="canAdmin_<?php echo $selectedEntity?>" value="<?php echo FilePermissions::PTYPE_ALL?>"<?php  if ($g->canAdmin()) { ?> checked<?php  } ?>></div></td>
			</tr>
		<?php  
			$rowNum++;
			} ?>
	<?php   }
				
	foreach ($uArray as $ui) {
		$selectedEntity = 'uID_' . $ui->getUserID(); ?>
	   <tr class="no-bg" id="_row:uID:<?php echo $ui->getUserID()?>">
			<td class="actor">
				<input type="hidden" name="selectedEntity[]" value="uID_<?php echo $ui->getUserID()?>" />
				<a href="javascript:removePermissionRow('_row:uID:<?php echo $ui->getUserID()?>')"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/remove.png" width="12" height="12"></a>
				<?php echo $ui->getUserName()?>                            
			</td>
			<td><div style="text-align: center"><input type="checkbox" name="canRead_<?php echo $selectedEntity?>" value="<?php echo FilePermissions::PTYPE_ALL?>"<?php  if ($ui->canRead()) { ?> checked<?php  } ?>></div></td>
			<td><div style="text-align: center"><input type="checkbox" name="canSearch_<?php echo $selectedEntity?>" value="<?php echo FilePermissions::PTYPE_ALL?>"<?php  if ($ui->canSearchFiles()) { ?> checked<?php  } ?>></div></td>
			<td><div style="text-align: center"><input type="checkbox" name="canWrite_<?php echo $selectedEntity?>" value="<?php echo FilePermissions::PTYPE_ALL?>"<?php  if ($ui->canWrite()) { ?> checked<?php  } ?>></div></td>
			<td><div style="text-align: center"><input type="checkbox" name="canAdmin_<?php echo $selectedEntity?>" value="<?php echo FilePermissions::PTYPE_ALL?>"<?php  if ($ui->canAdmin()) { ?> checked<?php  } ?>></div></td>
		</tr>
	<?php  
		$rowNum++;
		} ?>
</table>

	<div class="ccm-buttons">
	<?php  if ($f->overrideFileSetPermissions()) { ?>
		<input type="hidden" name="fRevertToSetPermissions" id="fRevertToSetPermissions" value="0" />
		<a href="javascript:void(0)" onclick="$('#fRevertToSetPermissions').val(1);ccm_alSubmitPermissionsForm('<?php echo $searchInstance?>')" class="ccm-button-left cancel"><span><?php echo t('Revert Permissions')?></span></a>
	<?php  } ?>
	<?php echo $ih->button_js(t('Update'), 'ccm_alSubmitPermissionsForm(\'' . $searchInstance . '\')')?>
	</div>


</form>

</div>
<?php  } ?>

<div id="ccm-file-password-tab" <?php  if (PERMISSIONS_MODEL != 'simple') { ?> style="display: none" <?php  } ?>>
<br/>

<h2><?php echo t('Requires Password to Access')?></h2>

<p><?php echo t('Leave the following form field blank in order to allow everyone to download this file.')?></p>

<form method="post" id="ccm-<?php echo $searchInstance?>-password-form" action="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/files/permissions/">
<?php echo $form->hidden('task', 'set_password')?>
<?php echo $form->hidden('fID', $f->getFileID())?>
<?php echo $ih->button_js(t('Save Password'), 'ccm_alSubmitPasswordForm(\'' . $searchInstance . '\')')?>
<?php echo $form->text('fPassword', $f->getPassword(), array('style' => 'width: 250px'))?>

</form>

<div class="ccm-spacer">&nbsp;</div>
<br/>
<div class="ccm-note"><?php echo t('Users who access files through the file manager will not be prompted for a password.')?></div>

</div>

<div id="ccm-file-storage-tab" style="display: none">

<br/>

<h2><?php echo t('Choose File Storage Location')?></h2>

<form method="post" id="ccm-<?php echo $searchInstance?>-storage-form" action="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/files/permissions/">
<?php echo $form->hidden('task', 'set_location')?>
<?php echo $form->hidden('fID', $f->getFileID())?>
<div><?php echo $form->radio('fslID', 0, $f->getStorageLocationID()) ?> <?php echo t('Default Location')?> (<?php echo DIR_FILES_UPLOADED?>)</div>

<?php 
$fsl = FileStorageLocation::getByID(FileStorageLocation::ALTERNATE_ID);
if (is_object($fsl)) { ?>
	<div><?php echo $form->radio('fslID', FileStorageLocation::ALTERNATE_ID, $f->getStorageLocationID()) ?> <?php echo $fsl->getName()?> (<?php echo $fsl->getDirectory()?>)</div>
<?php  } ?>
</form>

<div class="ccm-spacer">&nbsp;</div>
<?php echo $ih->button_js(t('Save Location'), 'ccm_alSubmitStorageForm(\'' . $searchInstance . '\')')?>
<div class="ccm-spacer">&nbsp;</div>

<br/>
<div class="ccm-note"><?php echo t('All versions of a file will be moved to the selected location.')?></div>



</div>

<script type="text/javascript">
	
<?php  if (PERMISSIONS_MODEL == 'simple') { ?>
	var ccm_fpActiveTab = "ccm-file-password";
<?php  } else { ?>
	var ccm_fpActiveTab = "ccm-file-permissions-advanced";
<?php  } ?>

$("#ccm-file-permissions-tabs a").click(function() {
	$("li.ccm-nav-active").removeClass('ccm-nav-active');
	$("#" + ccm_fpActiveTab + "-tab").hide();
	ccm_fpActiveTab = $(this).attr('id');
	$(this).parent().addClass("ccm-nav-active");
	$("#" + ccm_fpActiveTab + "-tab").show();
});



		function ccm_triggerSelectUser(uID, uName) {
		  rowValue = "uID_" + uID;
		  existingRow = document.getElementById("_row:" + rowValue);		  
		  if (!existingRow) {
		      tbl = document.getElementById("ccmPermissionsTable");	      
              row = tbl.insertRow(-1); // insert at bottom of table. safari, wtf ?                            
              row.id = "_row:" + rowValue;

			ccm_setupGridStriping('ccmPermissionsTable');
              
              cells = new Array();
				for (i = 0; i < 5; i++) {
					cells[i] = row.insertCell(i);
				}
				
				cells[0].className = "actor";
				cells[0].innerHTML = '<input type="hidden" name="selectedEntity[]" value="uID_' + uID + '" /><a href="javascript:removePermissionRow(\'_row:' + rowValue + '\')"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/remove.png" width="12" height="12"></a>' + uName;
				cells[1].innerHTML = '<div style="text-align: center"><input type="checkbox" name="canRead_' + rowValue + '" value="<?php echo FilePermissions::PTYPE_ALL?>"></div>';
				cells[2].innerHTML = '<div style="text-align: center"><input type="checkbox" name="canSearch_' + rowValue + '" value="<?php echo FilePermissions::PTYPE_ALL?>"></div>';
				cells[3].innerHTML = '<div style="text-align: center"><input type="checkbox" name="canWrite_' + rowValue + '" value="<?php echo FilePermissions::PTYPE_ALL?>"></div>';
				cells[4].innerHTML = '<div style="text-align: center"><input type="checkbox" name="canAdmin_' + rowValue + '" value="<?php echo FilePermissions::PTYPE_ALL?>"></div>';
             
            }
		}
		
		function ccm_triggerSelectGroup(gID, gName) {
	      // we add a row for the selected group
	      rowValue = "gID_" + gID;
	      rowText = gName;
          existingRow = document.getElementById("_row:" + rowValue);
          if (!existingRow) {
               
            tbl = document.getElementById("ccmPermissionsTable");	      
            row = tbl.insertRow(-1); // insert at bottom of table. safari, wtf ?                            
            row.id = "_row:" + rowValue;

			ccm_setupGridStriping('ccmPermissionsTable');

              
              cells = new Array();
				for (i = 0; i < 5; i++) {
					cells[i] = row.insertCell(i);
				}
              
              	cells[0].className = "actor";
              	cells[0].innerHTML = '<input type="hidden" name="selectedEntity[]" value="gID_' + gID + '" /><a href="javascript:removePermissionRow(\'_row:' + rowValue + '\',\'' + rowValue + '\',\'' + rowText + '\')"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/remove.png" width="12" height="12"></a>' + rowText;
				cells[1].innerHTML = '<div style="text-align: center"><input type="checkbox" name="canRead_' + rowValue + '" value="<?php echo FilePermissions::PTYPE_ALL?>"></div>';
				cells[2].innerHTML = '<div style="text-align: center"><input type="checkbox" name="canSearch_' + rowValue + '" value="<?php echo FilePermissions::PTYPE_ALL?>"></div>';
				cells[3].innerHTML = '<div style="text-align: center"><input type="checkbox" name="canWrite_' + rowValue + '" value="<?php echo FilePermissions::PTYPE_ALL?>"></div>';
				cells[4].innerHTML = '<div style="text-align: center"><input type="checkbox" name="canAdmin_' + rowValue + '" value="<?php echo FilePermissions::PTYPE_ALL?>"></div>';
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
		$("#ccm-<?php echo $searchInstance?>-storage-form").submit(function() {
			ccm_alSubmitStorageForm('<?php echo $searchInstance?>');
			return false;
		});
		$("#ccm-<?php echo $searchInstance?>-password-form").submit(function() {
			ccm_alSubmitPasswordForm('<?php echo $searchInstance?>');
			return false;
		});
		$("#ccm-<?php echo $searchInstance?>-permissions-form").submit(function() {
			ccm_alSubmitPermissionsForm('<?php echo $searchInstance?>');
			return false;
		});
	});
	
</script>
