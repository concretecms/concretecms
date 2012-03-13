<?
defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::helper("form");
$tp = new TaskPermission();
$dt = Loader::helper('form/date_time');
if (!$tp->canAccessUserSearch() && !$tp->canAccessGroupSearch()) { 
	die(t("Access Denied."));
}

if ($_REQUEST['peID']) {
	$pae = PermissionAccessEntity::getByID($_REQUEST['peID']);
} else {
	$pae = false;
}

if ($_REQUEST['pdID']) {
	$pd = PermissionDuration::getByID($_REQUEST['pdID']);
} else {
	$pd = false;
}

if ($_POST['task'] == 'save_permissions') { 
	$js = Loader::helper('json');
	$r = new stdClass;
	// First, we create a permissions access entity object for this
	
	if (isset($_POST['gID']) || isset($_POST['uID'])) { 
		if (!is_object($pae)) { 
			if (isset($_POST['uID'])) {
				$ui = UserInfo::getByID($_POST['uID']);
				if (is_object($ui)) { 
					$pae = UserPermissionAccessEntity::getOrCreate($ui);
				}
			} else {
				if (count($_POST['gID']) > 1) { 
					$groups = array();
					foreach($_POST['gID'] as $gID) {
						$g = Group::getByID($gID);
						if (is_object($g)) {
							$groups[] = $g;
						}
					}
					$pae = GroupCombinationPermissionAccessEntity::getOrCreate($groups);
				} else {
					$g = Group::getByID($_POST['gID'][0]);
					if (is_object($g)) {
						$pae = GroupPermissionAccessEntity::getOrCreate($g);			
					}
				}
			}
		}
		
		if (is_object($pae)) {

			$pd = PermissionDuration::translateFromRequest();
			
		} else {
			$r->error = true;
			$r->message = t('Unable to create permissions access entity object.');
		}

	} else {
		$r->error = true;
		$r->message = t('You must specify at least one user or group.');
	}
	
	if (!$r->error) {
		$r->peID = $pae->getAccessEntityID();
		if (is_object($pd)) {
			$r->pdID = $pd->getPermissionDurationID();
		} else {
			$r->pdID = 0;
		}
	}
	
	print $js->encode($r);
	exit;
}

?>
<div class="ccm-ui" id="ccm-permissions-access-entity-wrapper">

<form id="ccm-permissions-access-entity-form" method="post" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/permissions/access_entity">
<input type="hidden" name="task" value="save_permissions" />
<?=$form->hidden('accessType');?>
<?=$form->hidden('peID');?>
<?=$form->hidden('pdID');?>

<h4><?=t('Groups or Users')?></h4>

<p><?=t('Who gets access to this permission?')?></p>

<table id="ccm-permissions-access-entity-members">
<tr>
	<th><div style="width: 16px"></div></th>
	<th width="100%"><?=t("Name")?></th>
	<? if (!is_object($pae)) { ?>
		<th><div style="width: 16px"></div></th>
	<? } ?>
</tr>
<tr>
	<td colspan="<? if (!is_object($pae)) { ?>3<? } else { ?>2<? } ?>" id="ccm-permissions-access-entity-members-none"><?=t("No users or groups added.")?></td>
</tr>
</table>
<? if (!is_object($pae)) { ?>
<div style="margin-top: -10px" class="clearfix">
<input type="button" class="btn ccm-button-right small dialog-launch" id="ccm-permissions-access-entity-members-add-user" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/users/search_dialog?mode=choose_multiple&cID=<?=$_REQUEST['cID']?>" dialog-modal="false" dialog-width="90%" dialog-title="<?=t('Add User')?>"  dialog-height="70%" value="<?=t('Add User')?>" />
<input type="button" class="btn ccm-button-right small dialog-launch" id="ccm-permissions-access-entity-members-add-group" style="margin-right: 5px" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/select_group?cID=<?=$_REQUEST['cID']?>&include_core_groups=1" dialog-modal="false" dialog-title="<?=t('Add Group')?>" value="<?=t('Add Group')?>" />
</div>
<br/>
<? } ?>

<h4><?=t('Time Settings')?></h4>

<p><?=t('How long will this permission be valid for?')?></p>

<?=Loader::element('permission/duration', array('pd' => $pd)); ?>

<div class="dialog-buttons">
	<input type="button" onclick="jQuery.fn.dialog.closeTop()" value="<?=t('Cancel')?>" class="btn" />
	<input type="submit" onclick="$('#ccm-permissions-access-entity-form').submit()" value="<?=t('Save')?>" class="btn primary ccm-button-right" />
</div>
</form>

</div>

<script type="text/javascript">
ccm_accessEntityRemoveRow = function(link) {
	$(link).parent().parent().remove();
	var tbl = $("#ccm-permissions-access-entity-members");
	if (tbl.find('tr').length == 2) { 
		$("#ccm-permissions-access-entity-members-none").show();
		$("#ccm-permissions-access-entity-members-add-user").attr('disabled', false);
		$("#ccm-permissions-access-entity-members-add-group").attr('disabled', false);
	}
}
ccm_triggerSelectGroup = function(gID, gName) {
	if ($("input[class=entitygID][value=" + gID + "]").length == 0) { 
		$("#ccm-permissions-access-entity-members-none").hide();
		var tbl = $("#ccm-permissions-access-entity-members");
		html = '<tr><td><input type="hidden" class="entitygID" name="gID[]" value="' + gID + '" /><img src="<?=ASSETS_URL_IMAGES?>/icons/group.png" /></td><td>' + gName + '</td><? if (!is_object($pae)) { ?><td><a href="javascript:void(0)" onclick="ccm_accessEntityRemoveRow(this)"><img src="<?=ASSETS_URL_IMAGES?>/icons/remove.png" /></a></td><? } ?>';
		tbl.append(html);
		$("#ccm-permissions-access-entity-members-add-user").attr('disabled', true);
	}
}

ccm_triggerSelectUser = function(uID, uName) {
	$("#ccm-permissions-access-entity-members-none").hide();
	var tbl = $("#ccm-permissions-access-entity-members");
	html = '<tr><td><input type="hidden" name="uID" value="' + uID + '" /><img src="<?=ASSETS_URL_IMAGES?>/icons/user.png" /></td><td>' + uName + '</td><? if (!is_object($pae)) { ?><td><a href="javascript:void(0)" onclick="ccm_accessEntityRemoveRow(this)"><img src="<?=ASSETS_URL_IMAGES?>/icons/remove.png" /></a></td><? } ?>';
	tbl.append(html);
	$("#ccm-permissions-access-entity-members-add-group").attr('disabled', true);
	$("#ccm-permissions-access-entity-members-add-user").attr('disabled', true);
}


$(function() {
		
	$("#ccm-permissions-access-entity-form").ajaxForm({
		beforeSubmit: function(r) {
			jQuery.fn.dialog.showLoader();
		},
		success: function(r) {
			r = eval('(' + r + ')');
			jQuery.fn.dialog.hideLoader();
			if (r.error) {
				ccmAlert.notice('<?=t("Error")?>', r.message);
			} else { 

				if (typeof(ccm_addAccessEntity) == 'function') { 
					ccm_addAccessEntity(r.peID, r.pdID, '<?=$_REQUEST["accessType"]?>');
				} else {
					alert(r.peID);
					alert(r.pdID);
				}
			}
		}
	});
	

	<? if (is_object($pae)) { ?>
		<? switch($pae->getAccessEntityType()) {
			case 'C':
				foreach($pae->getGroups() as $g) { ?>
					ccm_triggerSelectGroup(<?=$g->getGroupID()?>, '<?=Loader::helper("text")->entities($g->getGroupName())?>');
				<? }
				break;
			case 'U':
				$ui = $pae->getUserObject(); ?>
				ccm_triggerSelectUser(<?=$ui->getUserID()?>, '<?=$ui->getUserName()?>');
				<?
				break;
			default:
				$g = $pae->getGroupObject(); ?>
				ccm_triggerSelectGroup(<?=$g->getGroupID()?>, '<?=Loader::helper("text")->entities($g->getGroupName())?>');
				<? break;
			} ?>
		<?
		} ?>

});

</script>