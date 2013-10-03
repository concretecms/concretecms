<?php defined('C5_EXECUTE') or die("Access Denied.");
$searchInstance = Loader::helper('text')->entities($_REQUEST['searchInstance']);
if(!strlen($searchInstance)) {
	$searchInstance = 'user';
}

$form = Loader::helper('form');
$ih = Loader::helper('concrete/interface');
$tp = new TaskPermission();

$sk = PermissionKey::getByHandle('access_user_search');
$gk = PermissionKey::getByHandle('assign_user_groups');

if (!$gk->validate()) {
	die(t("Access Denied."));
}

$users = array();
if (is_array($_REQUEST['uID'])) {
	foreach($_REQUEST['uID'] as $uID) {
		$ui = UserInfo::getByID($uID);
		$users[] = $ui;
	}
}

foreach($users as $ui) {
	if (!$sk->validate($ui)) { 
		die(t("Access Denied."));
	}
}

Loader::model('search/group');
$gl = new GroupSearch();
$gl->setItemsPerPage(-1);
$g1 = $gl->getPage();


if ($_POST['task'] == 'group_remove') {
	// build the group array
	$groupIDs = $_REQUEST['groupIDs'];
	$groups = array();
	if(is_array($groupIDs) && count($groupIDs)) {
		foreach($groupIDs as $gID) {
			$groups[] = Group::getByID($gID);			
		}
	}
	
	foreach($users as $ui) {
		if($ui instanceof UserInfo) {
			$u = $ui->getUserObject();
			foreach($groups as $g) {
				if ($gk->validate($g)) { 
					if($u->inGroup($g)) { // avoid messing up group enter times
						$u->exitGroup($g); 
					}				
				}
			}
		}
	}
	echo Loader::helper('json')->encode(array('error'=>false));
	exit;
}

if (!isset($_REQUEST['reload'])) { ?>
	<div id="ccm-user-bulk-group-remove-wrapper">
<? } ?>

	<div id="ccm-user-activate" class="ccm-ui">
		<form method="post" id="ccm-user-bulk-group-remove" action="<?php echo REL_DIR_FILES_TOOLS_REQUIRED ?>/users/bulk_group_remove">
			<fieldset class="form-stacked">
			<?php
			echo $form->hidden('task','group_remove');
			foreach($users as $ui) {
				echo $form->hidden('uID[]' , $ui->getUserID());
			}
			?>
			<div class="clearfix">
				<?=$form->label('groupIDs', t('Remove the users below from Group(s)'))?>
				<div class="input">
					<select multiple name="groupIDs[]" class="chosen-select" data-placeholder="<?php echo t('Select Group(s)');?>" >
						<? foreach($g1 as $g) { 
						if ($gk->validate($g['gID'])) { 
						
						?>
							<option value="<?=$g['gID']?>"  <? if (is_array($_REQUEST['groupIDs']) && in_array($g['gID'], $_REQUEST['groupIDs'])) { ?> selected="selected" <? } ?>><?=$g['gName']?></option>
						<? } 
						
						}?>
					</select>
				</div>
			</div>
			</fieldset>
			
			<?php Loader::element('users/confirm_list',array('users'=>$users)); ?>
		</form>
	

	
	</div>
	<div class="dialog-buttons">
		<?=$ih->button_js(t('Cancel'), 'jQuery.fn.dialog.closeTop()', 'left', 'btn')?>	
		<?=$ih->button_js(t('Save'), 'ccm_userBulkGroupRemove()', 'right', 'btn primary')?>
	</div>
<?
if (!isset($_REQUEST['reload'])) { ?>
</div>
<? } ?>

<script type="text/javascript">
ccm_userBulkGroupRemove = function() { 
	jQuery.fn.dialog.showLoader();
	$("#ccm-user-bulk-group-remove").ajaxSubmit(function(resp) {
		jQuery.fn.dialog.closeTop();
		jQuery.fn.dialog.hideLoader();
		ccm_deactivateSearchResults('<?=$searchInstance?>');
		ccmAlert.hud(ccmi18n.saveUserSettingsMsg, 2000, 'success', ccmi18n.user_group_remove);
		$("#ccm-<?=$searchInstance?>-advanced-search").ajaxSubmit(function(r) {
		       ccm_parseAdvancedSearchResponse(r, '<?=$searchInstance?>');
		});
	});
};
$(function() { 
	$(".chosen-select").chosen();	
});
</script>
