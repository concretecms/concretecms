<?php defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::helper('form');
$ih = Loader::helper('concrete/interface');
$tp = new TaskPermission();
if (!$tp->canAccessUserSearch()) { 
	die(t("Access Denied."));
}

$users = array();
if (is_array($_REQUEST['uID'])) {
	foreach($_REQUEST['uID'] as $uID) {
		$ui = UserInfo::getByID($uID);
		$users[] = $ui;
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
<? } ?>

	<div id="ccm-user-deactivate" class="ccm-ui">
		<form method="post" id="ccm-user-bulk-deactivate" action="<?php echo REL_DIR_FILES_TOOLS_REQUIRED ?>/users/bulk_deactivate">
			<?php
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
<?
if (!isset($_REQUEST['reload'])) { ?>
</div>
<? } ?>

<script type="text/javascript">
ccm_userBulkDeactivate = function() { 
	$("#ccm-user-bulk-deactivate").ajaxSubmit(function(resp) {
	});
	jQuery.fn.dialog.closeTop();
	ccm_setupUserSearch();
};
</script>
