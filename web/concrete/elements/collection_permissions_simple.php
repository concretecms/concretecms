<?
defined('C5_EXECUTE') or die("Access Denied.");
if ($cp->canEditPagePermissions()) {
	$editAccess = array();
	$viewAccess = array();
		
	$pk = PermissionKey::getByHandle('view_page');
	$pk->setPermissionObject($c);
	$assignments = $pk->getAccessListItems();
	foreach($assignments as $asi) {
		$ae = $asi->getAccessEntityObject();
		if ($ae->getAccessEntityTypeHandle() == 'group') {
			$group = $ae->getGroupObject();
			if (is_object($group)) {
				$viewAccess[] = $group->getGroupID();
			}
		}
	}

	$pk = PermissionKey::getByHandle('edit_page_contents');
	$pk->setPermissionObject($c);
	$assignments = $pk->getAccessListItems();
	foreach($assignments as $asi) {
		$ae = $asi->getAccessEntityObject();
		if ($ae->getAccessEntityTypeHandle() == 'group') {
			$group = $ae->getGroupObject();
			if (is_object($group)) {
				$editAccess[] = $group->getGroupID();
			}
		}
	}
	
	Loader::model('search/group');
	$gl = new GroupSearch();
	$gl->sortBy('gID', 'asc');
	$gIDs = $gl->get();
	$gArray = array();
	foreach($gIDs as $g) {
		$gArray[] = Group::getByID($g['gID']);
	}

	$rel = Loader::helper('security')->sanitizeString($_REQUEST['rel']);
?>

<div class="ccm-ui">
<form method="post" id="ccmPermissionsForm" name="ccmPermissionsForm" action="<?=$c->getCollectionAction()?>">
<input type="hidden" name="rel" value="<?php echo h($rel); ?>" />

<div class="clearfix">
<h3><?=t('Who can view this page?')?></h3>

<ul class="inputs-list">

<?

foreach ($gArray as $g) {
?>

<li><label><input type="checkbox" name="readGID[]" value="<?=$g->getGroupID()?>" <? if (in_array($g->getGroupID(), $viewAccess)) { ?> checked <? } ?> /> <?=t($g->getGroupName())?></label></li>

<? } ?>

</ul>
</div>

<div class="clearfix">

<h3><?=t('Who can edit this page?')?></h3>

<ul class="inputs-list">

<?

foreach ($gArray as $g) {
?>

<li><label><input type="checkbox" name="editGID[]" value="<?=$g->getGroupID()?>" <? if (in_array($g->getGroupID(), $editAccess)) { ?> checked <? } ?> /> <?=t($g->getGroupName())?></label></li>

<? } ?>

</ul>
</div>

<div class="dialog-buttons">
	<a href="javascript:void(0)" onclick="jQuery.fn.dialog.closeTop();" class="ccm-button-left btn"><?=t('Cancel')?></a>
	<a href="javascript:void(0)" onclick="$('form[name=ccmPermissionsForm]').submit()" class="ccm-button-right btn primary"><?=t('Save')?></a>
</div>	
<input type="hidden" name="update_permissions" value="1" class="accept">
<input type="hidden" name="processCollection" value="1">

<script type="text/javascript">
$(function() {
	$("#ccmPermissionsForm").ajaxForm({
		type: 'POST',
		iframe: true,
		beforeSubmit: function() {
			jQuery.fn.dialog.showLoader();
		},
		success: function(r) {
			var r = eval('(' + r + ')');
			jQuery.fn.dialog.hideLoader();
			jQuery.fn.dialog.closeTop();

			if (r != null && r.rel == 'SITEMAP') {
				ccmSitemapHighlightPageLabel(r.cID);
			}
			ccmAlert.hud(ccmi18n_sitemap.setPagePermissionsMsg, 2000, 'success', ccmi18n_sitemap.setPagePermissions);
		}
	});
});
</script>

<div class="ccm-spacer">&nbsp;</div>
</form>
</div>
<? } ?>