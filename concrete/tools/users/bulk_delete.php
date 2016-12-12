<?php defined('C5_EXECUTE') or die("Access Denied.");

$searchInstance = Loader::helper('text')->entities($_REQUEST['searchInstance']);
if(!strlen($searchInstance)) {
	$searchInstance = 'user';
}

$form = Loader::helper('form');
$ih = Loader::helper('concrete/ui');
$tp = new TaskPermission();
$u = new User();
$tp = new TaskPermission();
if (!$tp->canDeleteUser()) { 
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
		$up = new Permissions($ui);
		if(!$up->canViewUser() || (in_array($ui->getUserID(),$excluded_user_ids))) { 
			$excluded = true;
		} else {
			$users[] = $ui;
		}
	}
}

if ($_POST['task'] == 'delete') {
	foreach($users as $ui) {
		if(!(in_array($ui->getUserID(),$excluded_user_ids))) {
			$ui->delete();
		}
	}
	echo Loader::helper('json')->encode(array('error'=>false));
	exit;
} 

if (!isset($_REQUEST['reload'])) { ?>
	<div id="ccm-user-bulk-delete-wrapper">
<? } ?>

	<div id="ccm-user-delete" class="ccm-ui">
		<form method="post" id="ccm-user-bulk-delete" action="<?php echo REL_DIR_FILES_TOOLS_REQUIRED ?>/users/bulk_delete">
			<?php
			echo $form->hidden('task','delete');
			foreach($users as $ui) {
				echo $form->hidden('uID[]' , $ui->getUserID());
			}
			if($excluded) { ?>
				<div class="alert-message info">
					<?php echo t("Users you don't have permission to bulk-delete have been removed from this list.");	?>
				</div>
			<?php } ?>
			<?php echo t('Are you sure you would like to delete the following users?');?><br/><br/>
			<?php Loader::element('users/confirm_list',array('users'=>$users)); ?>
		</form>
	</div>
	<div class="dialog-buttons">
		<?=$ih->button_js(t('Cancel'), 'jQuery.fn.dialog.closeTop()', 'left', 'btn')?>	
		<?=$ih->button_js(t('Delete'), 'ccm_userBulkActivate()', 'right', 'btn error')?>
	</div>
<?
if (!isset($_REQUEST['reload'])) { ?>
</div>
<? } ?>

<script type="text/javascript">
ccm_userBulkActivate = function() { 
	jQuery.fn.dialog.showLoader();
	$("#ccm-user-bulk-delete").ajaxSubmit(function(resp) {
		jQuery.fn.dialog.closeTop();
		jQuery.fn.dialog.hideLoader();
		ccm_deactivateSearchResults('<?=$searchInstance?>');

		ConcreteAlert.notify({
		'message': ccmi18n.saveUserSettingsMsg,
		'title': ccmi18n.user_delete
		});

		$("#ccm-<?=$searchInstance?>-advanced-search").ajaxSubmit(function(r) {
		       ccm_parseAdvancedSearchResponse(r, '<?=$searchInstance?>');
		});
	});
	
};
</script>
