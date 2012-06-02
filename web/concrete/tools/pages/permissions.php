<?
defined('C5_EXECUTE') or die("Access Denied.");
$u = new User();
$form = Loader::helper('form');
$sh = Loader::helper('concrete/dashboard/sitemap');
if (!$sh->canRead()) {
	die(t('Access Denied'));
}

$form = Loader::helper('form');

$pages = array();
if (is_array($_REQUEST['cID'])) {
	foreach($_REQUEST['cID'] as $cID) {
		$pages[] = Page::getByID($cID);
	}
} else {
	$pages[] = Page::getByID($_REQUEST['cID']);
}

$pcnt = 0;
$cIDStr = '';
foreach($pages as $c) { 
	$cp = new Permissions($c);
	if ($cp->canEditPagePermissions()) {
		$cIDStr .= '&cID[]=' . $c->getCollectionID();
		$pcnt++;
	}
}

$searchInstance = Loader::helper('text')->entities($_REQUEST['searchInstance']);

?>
<div class="ccm-ui">

<? if ($pcnt == 0) { ?>
	<?=t("You do not have permission to change permissions on any of the selected pages."); ?>
<? } else {

	$dh = Loader::helper('date');
	$dt = Loader::helper('form/date_time');
	$editPermissions = true;
	$permissionsInherit = array();
	foreach($pages as $_c) {
		$permissionsInherit[] = $_c->getCollectionInheritance();
		$permissionsSubpageOverride[] = $_c->overrideTemplatePermissions();
	}
	$permissionsInherit = array_unique($permissionsInherit);
	$permissionsSubpageOverride = array_unique($permissionsSubpageOverride);
	if (count($permissionsInherit) == 1) {
		$permissionsInherit = $permissionsInherit[0];
	} else {
		$permissionsInherit = '-1';
	}
	if (count($permissionsSubpageOverride) == 1) {
		$permissionsSubpageOverride = $permissionsSubpageOverride[0];
	} else {
		$permissionsSubpageOverride = '-1';
	}

	if ($_REQUEST['subtask'] == 'set' && $permissionsInherit == 'OVERRIDE') { ?>
		<div id="ccm-page-permissions-list">
		
		<? $pk = PagePermissionKey::getByID($_REQUEST['pkID']);
		$pk->setPermissionObject($pages[0]);
		$pk->setMultiplePageArray($pages);
		
		?>
		
		<? if ($pk->getPermissionKeyDescription()) { ?>
		<div class="dialog-help">
		<?=$pk->getPermissionKeyDescription()?>
		</div>
		<? } ?>
		
		<? Loader::element('permission/message_list'); ?>
		
		<?
		$accessTypes = $pk->getSupportedAccessTypes();
		Loader::element('permission/access_list', array('permissionKey' => $pk, 'accessTypes' => $accessTypes)); ?>
		
		<? if ($pk->getPackageID() > 0) { ?>
			<? Loader::packageElement('permission/keys/' . $pk->getPermissionKeyHandle(), $pk->getPackageHandle(), array('permissionKey' => $pk)); ?>
		<? } else { ?>
			<? Loader::element('permission/keys/' . $pk->getPermissionKeyHandle(), array('permissionKey' => $pk)); ?>
		<? } ?>
		</div>
		
		<script type="text/javascript">
		ccm_addAccessEntity = function(peID, pdID, accessType) {
			jQuery.fn.dialog.closeTop();
			jQuery.fn.dialog.showLoader();
			
			$.get('<?=$pk->getPermissionAssignmentObject()->getPermissionKeyToolsURL("add_access_entity")?>&pdID=' + pdID + '&accessType=' + accessType + '&peID=' + peID, function(r) { 
				$.get('<?=REL_DIR_FILES_TOOLS_REQUIRED?>/pages/permissions?subtask=set&message=entity_added&pkID=<?=$pk->getPermissionKeyID()?><?=$cIDStr?>', function(r) { 
					jQuery.fn.dialog.replaceTop(r);
					jQuery.fn.dialog.hideLoader();
				});
			});
		}

		ccm_deleteAccessEntityAssignment = function(peID) {
			jQuery.fn.dialog.showLoader();
			$.get('<?=$pk->getPermissionAssignmentObject()->getPermissionKeyToolsURL("remove_access_entity")?>&peID=' + peID, function() { 
				$.get('<?=REL_DIR_FILES_TOOLS_REQUIRED?>/pages/permissions?subtask=set&message=entity_removed&pkID=<?=$pk->getPermissionKeyID()?><?=$cIDStr?>', function(r) { 
					jQuery.fn.dialog.replaceTop(r);
					jQuery.fn.dialog.hideLoader();
				});
			});
		}
		
		
		</script>	

	
	
	<? } else { ?>

	<form>
	
	<div class="ccm-pane-options" style="padding-bottom: 0px">
	<div class="clearfix">
	<label for="ccm-page-permissions-inherit"><?=t('Assign Permissions')?></label>
	<div class="input">
	   <select id="ccm-page-permissions-inherit" style="width: 220px">
	   <? if ($permissionsInherit == '-1') { ?>	<option value="-1" selected><?=t('** Multiple Settings')?></option><? } ?>
		<option value="PARENT" <? if ($permissionsInherit == 'PARENT') { ?>selected<? } ?>><?=t('By Area of Site (Hierarchy)')?></option>
		<option value="TEMPLATE" <? if ($permissionsInherit == 'TEMPLATE') { ?>selected<? } ?>><?=t('From Page Type Defaults')?></option>
		<option value="OVERRIDE" <? if ($permissionsInherit == 'OVERRIDE') { ?>selected<? } ?>><?=t('Manually')?></option>
	  </select>
	</div>
	</div>

	<div class="clearfix">
	<label for="ccm-page-permissions-subpages-override-template-permissions"><?=t('Subpage Permissions')?></label>
	<div class="input">
		<select id="ccm-page-permissions-subpages-override-template-permissions" style="width: 260px">
		   	<? if ($permissionsSubpageOverride == '-1') { ?><option value="-1" selected><?=t('** Multiple Settings')?></option><? } ?>
			<option value="0" <? if ($permissionsSubpageOverride == '0') { ?>selected<? } ?>><?=t('Inherit page type default permissions.')?></option>
			<option value="1" <? if ($permissionsSubpageOverride == '1') { ?>selected<? } ?>><?=t('Inherit the permissions of this page.')?></option>
		</select>
	</div>
	</div>
	
	</div>
	<br/>
	
	
	<? if ($permissionsInherit == 'OVERRIDE') { ?>

	<?=Loader::element('permission/help');?>
	
	<table class="ccm-permission-grid">
	<?
	$permissions = PermissionKey::getList('page');
	foreach($permissions as $pk) { 
		$pk->setPermissionObject($c);
		?>
		<tr>
		<td class="ccm-permission-grid-name"><strong><a dialog-width="500" dialog-height="380" dialog-on-destroy="ccm_refreshPagePermissions()" class="dialog-launch" dialog-title="<?=$pk->getPermissionKeyName()?>" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/pages/permissions?subtask=set&pkID=<?=$pk->getPermissionKeyID()?><?=$cIDStr?>"><?=$pk->getPermissionKeyName()?></a></td>
		<td><?=Loader::element('permission/labels', array('pk' => $pk))?></td>
	</tr>
	<? } ?>
	</table>
	
	<? } else { ?>
		<? $pkl = PermissionKey::getList('page'); $pk = $pkl[0];?>
		<p><?=t('You may only set specific permissions for pages if they are set to override defaults or their parent pages.')?></p>
	<? } ?>
	
	
	<div id="ccm-page-permissions-confirm-dialog" style="display: none">
	<?=t('Changing this setting will affect this page immediately. Are you sure?')?>
	<div id="dialog-buttons-start">
		<input type="button" class="btn" value="Cancel" onclick="jQuery.fn.dialog.closeTop()" />
		<input type="button" class="btn error ccm-button-right" value="Ok" onclick="ccm_pagePermissionsConfirmInheritanceChange()" />
	</div>
	</div>
	
	
	</form>
	
	<? 
		$pk->setMultiplePageArray($pages);
	?>
	
	<script type="text/javascript">
	var inheritanceVal = '';
	
	ccm_pagePermissionsCancelInheritance = function() {
		$('#ccm-page-permissions-inherit').val(inheritanceVal);
	}
	
	ccm_pagePermissionsConfirmInheritanceChange = function() { 
		jQuery.fn.dialog.showLoader();
		$.get('<?=$pk->getPermissionAssignmentObject()->getPermissionKeyToolsURL("change_permission_inheritance")?>&mode=' + $('#ccm-page-permissions-inherit').val(), function() { 
			jQuery.fn.dialog.closeTop();
			ccm_refreshPagePermissions();
		});
	}
	
	
	$(function() {
		inheritanceVal = $('#ccm-page-permissions-inherit').val();
		$('#ccm-page-permissions-inherit').change(function() {
			if ($(this).val() == '-1') { 
				ccm_pagePermissionsCancelInheritance();
			} else { 
				$('#dialog-buttons-start').addClass('dialog-buttons');
				jQuery.fn.dialog.open({
					element: '#ccm-page-permissions-confirm-dialog',
					title: '<?=t("Confirm Change")?>',
					width: 280,
					height: 100,
					onClose: function() {
						ccm_pagePermissionsCancelInheritance();
					}
				});
			}
		});
		
		$('#ccm-page-permissions-subpages-override-template-permissions').change(function() {
			jQuery.fn.dialog.showLoader();
			$.get('<?=$pk->getPermissionAssignmentObject()->getPermissionKeyToolsURL("change_subpage_defaults_inheritance")?>&inherit=' + $(this).val(), function() { 
				ccm_refreshPagePermissions();
			});
		});
		
	});
	
	ccm_refreshPagePermissions = function() {
		jQuery.fn.dialog.showLoader();
		$.get('<?=REL_DIR_FILES_TOOLS_REQUIRED?>/pages/permissions?foo=1<?=$cIDStr?>', function(r) { 
			jQuery.fn.dialog.replaceTop(r);
			jQuery.fn.dialog.hideLoader();
		});	
	}
	
	</script>
	
	
<? }

?>
</div>

<? } ?>
