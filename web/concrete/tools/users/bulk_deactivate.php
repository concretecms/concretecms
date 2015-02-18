<?php defined('C5_EXECUTE') or die("Access Denied.");

$searchInstance = Loader::helper('text')->entities($_REQUEST['searchInstance']);
if(!strlen($searchInstance)) {
	$searchInstance = 'user';
}

$sk = PermissionKey::getByHandle('access_user_search');
$ek = PermissionKey::getByHandle('activate_user');

$form = Loader::helper('form');
$ih = Loader::helper('concrete/ui');
$tp = new TaskPermission();
if (!$tp->canActivateUser()) { 
	die(t("Access Denied."));
}

$users = array();
$excluded = false;
$excluded_user_ids = array();
$excluded_user_ids[] = $u->getUserID(); // can't delete yourself
$excluded_user_ids[] = USER_SUPER_ID; // can't delete the super user (admin)

if (is_array($_REQUEST['uID'])) {
	foreach($_REQUEST['uID'] as $uID) {
		$ui = UserInfo::getByID($uID);
		
		if(!$sk->validate($ui) || (in_array($ui->getUserID(),$excluded_user_ids))) { 
			$excluded = true;
		} else {
			$users[] = $ui;
		}
	}
}

if ($_POST['task'] == 'deactivate') {
	foreach($users as $ui) {
		if($ui->isActive()) {
			$ui->deactivate();
		}
	}
	echo Loader::helper('json')->encode(array('error'=>false));
	exit;
}

if (!isset($_REQUEST['reload'])) { ?>
	<div id="ccm-user-bulk-deactivate-wrapper">
<?php } ?>

	<div id="ccm-user-deactivate" class="ccm-ui">
		<form method="post" id="ccm-user-bulk-deactivate" action="<?php echo REL_DIR_FILES_TOOLS_REQUIRED ?>/users/bulk_deactivate">
			<?php if($excluded) { ?>
				<div class="alert-message info">
					<?php echo t("Users you don't have permission to bulk-deactivate have been removed from this list.");	?>
				</div>
			<?php }
			
			echo $form->hidden('task','deactivate');
			foreach($users as $ui) {
				echo $form->hidden('uID[]' , $ui->getUserID());
			}
			?>
			<?php echo t('Are you sure you would like to deactivate the following users?');?><br/><br/>
			<?php Loader::element('users/confirm_list',array('users'=>$users)); ?>
		</form>
	</div>
	<div class="dialog-buttons">
		<?=$ih->button_js(t('Cancel'), 'jQuery.fn.dialog.closeTop()', 'left', 'btn')?>	
		<?=$ih->button_js(t('Deactivate'), 'ccm_userBulkDeactivate()', 'right', 'btn primary')?>
	</div>
<?php
if (!isset($_REQUEST['reload'])) { ?>
</div>
<?php } ?>

<script type="text/javascript">
ccm_userBulkDeactivate = function() { 
	jQuery.fn.dialog.showLoader();
	$("#ccm-user-bulk-deactivate").ajaxSubmit(function(resp) {
		jQuery.fn.dialog.closeTop();
		jQuery.fn.dialog.showLoader();
		jQuery.fn.dialog.hideLoader();
		ccm_deactivateSearchResults('<?=$searchInstance?>');
		ConcreteAlert.notify({
		'message': ccmi18n.saveUserSettingsMsg,
		'title': ccmi18n.user_deactivate
		});
		$("#ccm-<?=$searchInstance?>-advanced-search").ajaxSubmit(function(r) {
		       ccm_parseAdvancedSearchResponse(r, '<?=$searchInstance?>');
		});
	});
	
};
</script>
