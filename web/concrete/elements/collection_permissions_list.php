<?
defined('C5_EXECUTE') or die("Access Denied.");
Loader::model('collection_types');
$dh = Loader::helper('date');
$dt = Loader::helper('form/date_time');
$editPermissions = false;
if ($c->getCollectionInheritance() == 'OVERRIDE') { 
	$editPermissions = true;
}
?>
<div class="ccm-ui">
<form>

<div class="ccm-pane-options" style="padding-bottom: 0px">
<div class="clearfix">
<label for="ccm-page-permissions-inherit"><?=t('Assign Permissions')?></label>
<div class="input">
   <select id="ccm-page-permissions-inherit" style="width: 180px">
	<? if ($c->getCollectionID() > 1) { ?><option value="PARENT" <? if ($c->getCollectionInheritance() == "PARENT") { ?> selected<? } ?>><?=t('By Area of Site (Hierarchy)')?></option><? } ?>
	<? if ($c->getMasterCollectionID() > 1) { ?><option value="TEMPLATE"  <? if ($c->getCollectionInheritance() == "TEMPLATE") { ?> selected<? } ?>><?=t('From Page Type Defaults')?></option><? } ?>
	<option value="OVERRIDE" <? if ($c->getCollectionInheritance() == "OVERRIDE") { ?> selected<? } ?>><?=t('Manually')?></option>
  </select>
</div>
</div>
<? if (!$c->isMasterCollection()) { ?>
<div class="clearfix">
<label for="ccm-page-permissions-subpages-override-template-permissions"><?=t('Subpage Permissions')?></label>
<div class="input">
	<select id="ccm-page-permissions-subpages-override-template-permissions" style="width: 260px">
		<option value="0"<? if (!$c->overrideTemplatePermissions()) { ?>selected<? } ?>><?=t('Inherit page type default permissions.')?></option>
		<option value="1"<? if ($c->overrideTemplatePermissions()) { ?>selected<? } ?>><?=t('Inherit the permissions of this page.')?></option>
	</select>
</div>
</div>

<? } ?>
</div>
<br/>

<?
	  $cpc = $c->getPermissionsCollectionObject();
	if ($c->getCollectionInheritance() == "PARENT") { ?>
	<div><strong><?=t('This page inherits its permissions from:');?> <a target="_blank" href="<?=DIR_REL?>/<?=DISPATCHER_FILENAME?>?cID=<?=$cpc->getCollectionID()?>"><?=$cpc->getCollectionName()?></a></strong></div><br/><br/>
	<? } ?>		


<?=Loader::element('permission/help');?>

<table class="ccm-permission-grid">
<?
$permissions = PermissionKey::getList('page');
foreach($permissions as $pk) { 
	$pk->setPermissionObject($c);
	?>
	<tr>
	<td class="ccm-permission-grid-name"><strong><? if ($editPermissions) { ?><a dialog-width="500" dialog-height="380" dialog-on-destroy="ccm_refreshPagePermissions()" class="dialog-launch" dialog-title="<?=t('Permissions')?>" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_collection_popup?cID=<?=$c->getCollectionID()?>&ctask=set_advanced_permissions&pkID=<?=$pk->getPermissionKeyID()?>"><? } ?><?=$pk->getPermissionKeyName()?><? if ($editPermissions) { ?></a><? } ?></td>
	<td><?=Loader::element('permission/labels', array('pk' => $pk))?></td>
</tr>
<? } ?>
</table>

</div>

<script type="text/javascript">
$(function() {
	$('#ccm-page-permissions-inherit').change(function() {
		jQuery.fn.dialog.showLoader();
		$.get('<?=$pk->getPermissionKeyToolsURL("change_permission_inheritance")?>&cID=<?=$c->getCollectionID()?>&mode=' + $(this).val(), function() { 
			ccm_refreshPagePermissions();
		});
	});
	
	$('#ccm-page-permissions-subpages-override-template-permissions').change(function() {
		jQuery.fn.dialog.showLoader();
		$.get('<?=$pk->getPermissionKeyToolsURL("change_subpage_defaults_inheritance")?>&cID=<?=$c->getCollectionID()?>&inherit=' + $(this).val(), function() { 
			ccm_refreshPagePermissions();
		});
	});
	
});

ccm_refreshPagePermissions = function() {
	jQuery.fn.dialog.showLoader();
	$.get('<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_collection_popup?ctask=edit_permissions&cID=<?=$c->getCollectionID()?>', function(r) { 
		jQuery.fn.dialog.replaceTop(r);
		jQuery.fn.dialog.hideLoader();
	});	
}

</script>