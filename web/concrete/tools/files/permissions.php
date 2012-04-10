<?
defined('C5_EXECUTE') or die("Access Denied.");
$u = new User();
$form = Loader::helper('form');
$searchInstance = $_REQUEST['searchInstance'];
$ih = Loader::helper('concrete/interface'); 
$f = File::getByID($_REQUEST['fID']);
$cp = new Permissions($f);
if (!$cp->canAdmin()) {
	die(t("Access Denied."));
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

<div class="ccm-ui">

<ul class="tabs" id="ccm-file-permissions-tabs">
	<? if (PERMISSIONS_MODEL != 'simple') { ?>
		<li class="active"><a href="javascript:void(0)" id="ccm-file-permissions-advanced"><?=t('Permissions')?></a></li>
	<? } ?>
	<li <? if (PERMISSIONS_MODEL == 'simple') { ?> class="active" <? } ?>><a href="javascript:void(0)" id="ccm-file-password"><?=t('Protect with Password')?></a></li>
	<li><a href="javascript:void(0)" id="ccm-file-storage"><?=t('Storage Location')?></a></li>
</ul>

<div class="clearfix"></div>

<? if (PERMISSIONS_MODEL != 'simple') { ?>

<div id="ccm-file-permissions-advanced-tab">

<form method="post" id="ccm-<?=$searchInstance?>-permissions-form" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/permissions/">
<?=$form->hidden('task', 'set_advanced_permissions')?>
<?=$form->hidden('fID', $f->getFileID())?>

<div class="clearfix" style="margin-bottom: 0px">
<div class="block-message alert-message info">
<? if (!$f->overrideFileSetPermissions()) { ?>
	<p><?=t('Permissions for this file are currently dependent on set and global settings. If you override those permissions here, they will not match those of the file\'s sets.')?></p>
<? } else { ?>
	<p><?=t("Permissions for this file currently override file set and global settings. To revert these permissions, click the button below.")?></p>
<? } ?>	
</div>
</div>
<div class="clearfix">
<a class="btn ccm-button-right dialog-launch ug-selector" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/users/search_dialog?mode=choose_multiple" dialog-modal="false" dialog-width="90%" dialog-title="<?=t('Add User')?>"  dialog-height="70%"><?=t('Add User')?></a>
<a class="btn ccm-button-right dialog-launch ug-selector" style="margin-right: 5px" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/select_group" dialog-modal="false" dialog-title="<?=t('Add Group')?>"><?=t('Add Group')?></a>
</div>

<? if ($f->overrideFileSetPermissions()) {
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
	  <th><?=t('Read')?></th>
	  <th><?=t('Search')?></th>
	  <th><?=t('Write')?></th>
	  <th><?=t('Admin')?></th>
			 
	</tr>
	<? 
	$rowNum = 1;
	foreach ($gArray as $g) { 
		$displayRow = false;
		$selectedEntity = 'gID_' . $g->getGroupID();
		$display = (($g->getGroupID() == GUEST_GROUP_ID || $g->getGroupID() == REGISTERED_GROUP_ID) || $g->canRead() || $g->canWrite() || $g->canAdmin()) 
		? true : false;
			   
		if ($display) { ?>
			<tr class="no-bg" id="_row:gID:<?=$g->getGroupID()?>">
				<td class="actor">
					<input type="hidden" name="selectedEntity[]" value="gID_<?=$g->getGroupID()?>" />
					<? if ($g->getGroupID() != GUEST_GROUP_ID && $g->getGroupID() != REGISTERED_GROUP_ID) { ?>    
						<a href="javascript:removePermissionRow('_row:gID:<?=$g->getGroupID()?>','gID:<?=$g->getGroupID()?>', '<?=$g->getGroupName()?>')"><img src="<?=ASSETS_URL_IMAGES?>/icons/remove.png" width="12" height="12"></a>
					<? } ?>
					<?=$g->getGroupName()?>                            
				</td>
				<td><div style="text-align: center"><input type="checkbox" name="canRead_<?=$selectedEntity?>" value="<?=FilePermissions::PTYPE_ALL?>"<? if ($g->canRead()) { ?> checked<? } ?>></div></td>
				<td><div style="text-align: center"><input type="checkbox" name="canSearch_<?=$selectedEntity?>" value="<?=FilePermissions::PTYPE_ALL?>"<? if ($g->canSearchFiles()) { ?> checked<? } ?>></div></td>
				<td><div style="text-align: center"><input type="checkbox" name="canWrite_<?=$selectedEntity?>" value="<?=FilePermissions::PTYPE_ALL?>"<? if ($g->canWrite()) { ?> checked<? } ?>></div></td>
				<td><div style="text-align: center"><input type="checkbox" name="canAdmin_<?=$selectedEntity?>" value="<?=FilePermissions::PTYPE_ALL?>"<? if ($g->canAdmin()) { ?> checked<? } ?>></div></td>
			</tr>
		<? 
			$rowNum++;
			} ?>
	<?  }
				
	foreach ($uArray as $ui) {
		$selectedEntity = 'uID_' . $ui->getUserID(); ?>
	   <tr class="no-bg" id="_row:uID:<?=$ui->getUserID()?>">
			<td class="actor">
				<input type="hidden" name="selectedEntity[]" value="uID_<?=$ui->getUserID()?>" />
				<a href="javascript:removePermissionRow('_row:uID:<?=$ui->getUserID()?>')"><img src="<?=ASSETS_URL_IMAGES?>/icons/remove.png" width="12" height="12"></a>
				<?=$ui->getUserName()?>                            
			</td>
			<td><div style="text-align: center"><input type="checkbox" name="canRead_<?=$selectedEntity?>" value="<?=FilePermissions::PTYPE_ALL?>"<? if ($ui->canRead()) { ?> checked<? } ?>></div></td>
			<td><div style="text-align: center"><input type="checkbox" name="canSearch_<?=$selectedEntity?>" value="<?=FilePermissions::PTYPE_ALL?>"<? if ($ui->canSearchFiles()) { ?> checked<? } ?>></div></td>
			<td><div style="text-align: center"><input type="checkbox" name="canWrite_<?=$selectedEntity?>" value="<?=FilePermissions::PTYPE_ALL?>"<? if ($ui->canWrite()) { ?> checked<? } ?>></div></td>
			<td><div style="text-align: center"><input type="checkbox" name="canAdmin_<?=$selectedEntity?>" value="<?=FilePermissions::PTYPE_ALL?>"<? if ($ui->canAdmin()) { ?> checked<? } ?>></div></td>
		</tr>
	<? 
		$rowNum++;
		} ?>
</table>

	<div class="ccm-buttons">
	<? if ($f->overrideFileSetPermissions()) { ?>
		<input type="hidden" name="fRevertToSetPermissions" id="fRevertToSetPermissions" value="0" />
		<a href="javascript:void(0)" onclick="$('#fRevertToSetPermissions').val(1);ccm_alSubmitPermissionsForm('<?=$searchInstance?>')" class="ccm-button-left cancel btn"><?=t('Revert Permissions')?></a>
	<? } ?>
	<?=$ih->button_js(t('Update'), 'ccm_alSubmitPermissionsForm(\'' . $searchInstance . '\')', 'right', 'primary')?>
	</div>


</form>

</div>
<? } ?>

<div id="ccm-file-password-tab" <? if (PERMISSIONS_MODEL != 'simple') { ?> style="display: none" <? } ?>>
<br/>

<h4><?=t('Requires Password to Access')?></h4>

<p><?=t('Leave the following form field blank in order to allow everyone to download this file.')?></p>

<form method="post" id="ccm-<?=$searchInstance?>-password-form" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/permissions/">
<?=$form->hidden('task', 'set_password')?>
<?=$form->hidden('fID', $f->getFileID())?>
<?=$form->text('fPassword', $f->getPassword(), array('style' => 'width: 250px'))?>

<?=$ih->button_js(t('Save Password'), 'ccm_alSubmitPasswordForm(\'' . $searchInstance . '\')', 'left', 'primary')?>

</form>

<div class="help-block"><p><?=t('Users who access files through the file manager will not be prompted for a password.')?></p></div>

</div>

<div id="ccm-file-storage-tab" style="display: none">

<br/>

<h4><?=t('Choose File Storage Location')?></h4>

<form method="post" id="ccm-<?=$searchInstance?>-storage-form" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/permissions/">
<div class="help-block"><p><?=t('All versions of a file will be moved to the selected location.')?></p></div>

<?=$form->hidden('task', 'set_location')?>
<?=$form->hidden('fID', $f->getFileID())?>
<div><?=$form->radio('fslID', 0, $f->getStorageLocationID()) ?> <?=t('Default Location')?> (<?=DIR_FILES_UPLOADED?>)</div>

<?
$fsl = FileStorageLocation::getByID(FileStorageLocation::ALTERNATE_ID);
if (is_object($fsl)) { ?>
	<div><?=$form->radio('fslID', FileStorageLocation::ALTERNATE_ID, $f->getStorageLocationID()) ?> <?=$fsl->getName()?> (<?=$fsl->getDirectory()?>)</div>
<? } ?>
</form>


<?=$ih->button_js(t('Save Location'), 'ccm_alSubmitStorageForm(\'' . $searchInstance . '\')', 'left', 'primary')?>




</div>

</div>

<script type="text/javascript">
	
<? if (PERMISSIONS_MODEL == 'simple') { ?>
	var ccm_fpActiveTab = "ccm-file-password";
<? } else { ?>
	var ccm_fpActiveTab = "ccm-file-permissions-advanced";
<? } ?>

$("#ccm-file-permissions-tabs a").click(function() {
	$("li.active").removeClass('active');
	$("#" + ccm_fpActiveTab + "-tab").hide();
	ccm_fpActiveTab = $(this).attr('id');
	$(this).parent().addClass("active");
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
				cells[0].innerHTML = '<input type="hidden" name="selectedEntity[]" value="uID_' + uID + '" /><a href="javascript:removePermissionRow(\'_row:' + rowValue + '\')"><img src="<?=ASSETS_URL_IMAGES?>/icons/remove.png" width="12" height="12"></a>' + uName;
				cells[1].innerHTML = '<div style="text-align: center"><input type="checkbox" name="canRead_' + rowValue + '" value="<?=FilePermissions::PTYPE_ALL?>"></div>';
				cells[2].innerHTML = '<div style="text-align: center"><input type="checkbox" name="canSearch_' + rowValue + '" value="<?=FilePermissions::PTYPE_ALL?>"></div>';
				cells[3].innerHTML = '<div style="text-align: center"><input type="checkbox" name="canWrite_' + rowValue + '" value="<?=FilePermissions::PTYPE_ALL?>"></div>';
				cells[4].innerHTML = '<div style="text-align: center"><input type="checkbox" name="canAdmin_' + rowValue + '" value="<?=FilePermissions::PTYPE_ALL?>"></div>';
             
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
              	cells[0].innerHTML = '<input type="hidden" name="selectedEntity[]" value="gID_' + gID + '" /><a href="javascript:removePermissionRow(\'_row:' + rowValue + '\',\'' + rowValue + '\',\'' + rowText + '\')"><img src="<?=ASSETS_URL_IMAGES?>/icons/remove.png" width="12" height="12"></a>' + rowText;
				cells[1].innerHTML = '<div style="text-align: center"><input type="checkbox" name="canRead_' + rowValue + '" value="<?=FilePermissions::PTYPE_ALL?>"></div>';
				cells[2].innerHTML = '<div style="text-align: center"><input type="checkbox" name="canSearch_' + rowValue + '" value="<?=FilePermissions::PTYPE_ALL?>"></div>';
				cells[3].innerHTML = '<div style="text-align: center"><input type="checkbox" name="canWrite_' + rowValue + '" value="<?=FilePermissions::PTYPE_ALL?>"></div>';
				cells[4].innerHTML = '<div style="text-align: center"><input type="checkbox" name="canAdmin_' + rowValue + '" value="<?=FilePermissions::PTYPE_ALL?>"></div>';
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
		$("#ccm-<?=$searchInstance?>-storage-form").submit(function() {
			ccm_alSubmitStorageForm('<?=$searchInstance?>');
			return false;
		});
		$("#ccm-<?=$searchInstance?>-password-form").submit(function() {
			ccm_alSubmitPasswordForm('<?=$searchInstance?>');
			return false;
		});
		$("#ccm-<?=$searchInstance?>-permissions-form").submit(function() {
			ccm_alSubmitPermissionsForm('<?=$searchInstance?>');
			return false;
		});
	});
	
</script>
