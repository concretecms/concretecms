<?php defined('C5_EXECUTE') or die("Access Denied.");
$searchInstance = $_REQUEST['searchInstance'];
if(!strlen($searchInstance)) {
	$searchInstance = 'user';
}

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

if ($_POST['task'] == 'activate') {
	foreach($users as $ui) {
		if(!$ui->isActive()) {
			$ui->activate();
		}
	}
	echo Loader::helper('json')->encode(array('error'=>false));
	exit;
} 

if (!isset($_REQUEST['reload'])) { ?>
	<div id="ccm-user-bulk-activate-wrapper">
<? } ?>

	<div id="ccm-user-activate" class="ccm-ui">
		<form method="post" id="ccm-user-bulk-activate" action="<?php echo REL_DIR_FILES_TOOLS_REQUIRED ?>/users/bulk_activate">
			<?php
			echo $form->hidden('task','activate');
			foreach($users as $ui) {
				echo $form->hidden('uID[]' , $ui->getUserID());
			}
			?>
			<div class="clearfix">
				<div class="input">
					<label><?php echo t('Select Group')?></label>
					
				</div>
			</div>
			
			<?php echo t('Are you sure you would like to activate the following users?');?><br/><br/>
			<?php Loader::element('users/confirm_list',array('users'=>$users)); ?>
		</form>	
	</div>
	<div class="dialog-buttons">
		<?=$ih->button_js(t('Cancel'), 'jQuery.fn.dialog.closeTop()', 'left', 'btn')?>	
		<?=$ih->button_js(t('Activate'), 'ccm_userBulkActivate()', 'right', 'btn primary')?>
	</div>
<?
if (!isset($_REQUEST['reload'])) { ?>
</div>
<? } ?>

<script type="text/javascript">
ccm_userBulkActivate = function() { 
	$("#ccm-user-bulk-activate").ajaxSubmit(function(resp) {
		jQuery.fn.dialog.closeTop();
		jQuery.fn.dialog.hideLoader();
		ccm_deactivateSearchResults('<?=$searchInstance?>');
		ccmAlert.hud(ccmi18n.saveUserSettingsMsg, 2000, 'success', ccmi18n.user_activate);
		$("#ccm-<?=$searchInstance?>-advanced-search").ajaxSubmit(function(r) {
		       ccm_parseAdvancedSearchResponse(r, '<?=$searchInstance?>');
		});
	});
};
</script>
