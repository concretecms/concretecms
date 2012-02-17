<?
defined('C5_EXECUTE') or die("Access Denied.");

$tp = new TaskPermission();
if (!$tp->canAccessUserSearch() && !$tp->canAccessGroupSearch()) { 
	die(t("Access Denied."));
}

?>
<div class="ccm-ui">

<table id="ccm-permissions-access-entity-members">
<tr>
	<th><div style="width: 16px"></div></th>
	<th width="100%"><?=t("Name")?></th>
	<th><div style="width: 16px"></div></th>
</tr>
<tr>
	<td colspan="3" id="ccm-permissions-access-entity-members-none"><?=t("No users or groups added.")?></td>
</tr>
</table>

<input type="button" class="btn ccm-button-right dialog-launch" id="ccm-permissions-access-entity-members-add-user" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/users/search_dialog?mode=choose_multiple&cID=<?=$_REQUEST['cID']?>" dialog-modal="false" dialog-width="90%" dialog-title="<?=t('Add User')?>"  dialog-height="70%" value="<?=t('Add User')?>" />
<input type="button" class="btn ccm-button-right dialog-launch" id="ccm-permissions-access-entity-members-add-group" style="margin-right: 5px" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/select_group?cID=<?=$_REQUEST['cID']?>" dialog-modal="false" dialog-title="<?=t('Add Group')?>" value="<?=t('Add Group')?>" />

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
		html = '<tr><td><input type="hidden" class="entitygID" name="gID[]" value="' + gID + '" /><img src="<?=ASSETS_URL_IMAGES?>/icons/group.png" /></td><td>' + gName + '</td><td><a href="javascript:void(0)" onclick="ccm_accessEntityRemoveRow(this)"><img src="<?=ASSETS_URL_IMAGES?>/icons/remove.png" /></a></td>';
		tbl.append(html);
		$("#ccm-permissions-access-entity-members-add-user").attr('disabled', true);
	}
}

ccm_triggerSelectUser = function(uID, uName) {
	$("#ccm-permissions-access-entity-members-none").hide();
	var tbl = $("#ccm-permissions-access-entity-members");
	html = '<tr><td><input type="hidden" name="uID[]" value="' + uID + '" /><img src="<?=ASSETS_URL_IMAGES?>/icons/user.png" /></td><td>' + uName + '</td><td><a href="javascript:void(0)" onclick="ccm_accessEntityRemoveRow(this)"><img src="<?=ASSETS_URL_IMAGES?>/icons/remove.png" /></a></td>';
	tbl.append(html);
	$("#ccm-permissions-access-entity-members-add-group").attr('disabled', true);
	$("#ccm-permissions-access-entity-members-add-user").attr('disabled', true);
}

</script>