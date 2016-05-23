<?php defined('C5_EXECUTE') or die("Access Denied.");
$searchInstance = Loader::helper('text')->entities($_REQUEST['searchInstance']);
if (!strlen($searchInstance)) {
    $searchInstance = 'user';
}

$form = Loader::helper('form');
$ih = Loader::helper('concrete/ui');
$tp = new TaskPermission();

$users = array();
if (is_array($_REQUEST['uID'])) {
    foreach ($_REQUEST['uID'] as $uID) {
        $ui = UserInfo::getByID($uID);
        $users[] = $ui;
    }
}

foreach ($users as $ui) {
    $up = new Permissions($ui);
    if (!$up->canViewUser()) {
        die(t("Access Denied."));
    }
}

$gl = new GroupList();
$g1 = $gl->getResults();

if ($_POST['task'] == 'group_add') {
    // build the group array
    $groupIDs = $_REQUEST['groupIDs'];
    $groups = array();
    if (is_array($groupIDs) && count($groupIDs)) {
        foreach ($groupIDs as $gID) {
            $groups[] = Group::getByID($gID);
        }
    }

    foreach ($users as $ui) {
        if ($ui instanceof UserInfo) {
            $u = $ui->getUserObject();
            foreach ($groups as $g) {
                $gp = new Permissions($g);
                if ($gp->canAssignGroup()) {
                    if (!$u->inGroup($g)) { // avoid messing up group enter times
                        $u->enterGroup($g);
                    }
                }
            }
        }
    }
    echo Loader::helper('json')->encode(array('error' => false));
    exit;
}

if (!isset($_REQUEST['reload'])) {
    ?>
	<div id="ccm-user-bulk-group-add-wrapper">
<?php 
} ?>

	<div id="ccm-user-activate" class="ccm-ui">
		<form method="post" id="ccm-user-bulk-group-add" action="<?php echo REL_DIR_FILES_TOOLS_REQUIRED ?>/users/bulk_group_add">
			<fieldset class="form-stacked">
			<?php
            echo $form->hidden('task', 'group_add');
            foreach ($users as $ui) {
                echo $form->hidden('uID[]', $ui->getUserID());
            }
            ?>
			<div class="clearfix">
				<?=$form->label('groupIDs', t('Add the users below to Group(s)'))?>
				<div class="input">
					<select multiple name="groupIDs[]" class="select2-select" data-placeholder="<?php echo t('Select Group(s)');?>" >
						<?php foreach ($g1 as $gRow) {
    $g = Group::getByID($gRow['gID']);
    $gp = new Permissions($g);
    if ($gp->canAssignGroup()) {
        ?>
							<option value="<?=$g->getGroupID()?>"  <?php if (is_array($_REQUEST['groupIDs']) && in_array($g->getGroupID(), $_REQUEST['groupIDs'])) {
    ?> selected="selected" <?php 
}
        ?>><?=$g->getGroupDisplayName()?></option>
						<?php 
    }
}?>
					</select>
				</div>
			</div>
			</fieldset>
			
			<?php Loader::element('users/confirm_list', array('users' => $users)); ?>
		</form>
	

	
	</div>
	<div class="dialog-buttons">
		<?=$ih->button_js(t('Cancel'), 'jQuery.fn.dialog.closeTop()', 'left', 'btn')?>	
		<?=$ih->button_js(t('Save'), 'ccm_userBulkGroupAdd()', 'right', 'btn primary')?>
	</div>
<?php
if (!isset($_REQUEST['reload'])) {
    ?>
</div>
<?php 
} ?>

<script type="text/javascript">
ccm_userBulkGroupAdd = function() { 
	jQuery.fn.dialog.showLoader();
	$("#ccm-user-bulk-group-add").ajaxSubmit(function(resp) {
		jQuery.fn.dialog.closeTop();
		jQuery.fn.dialog.hideLoader();
		ccm_deactivateSearchResults('<?=$searchInstance?>');
		ConcreteAlert.notify({
		'message': ccmi18n.saveUserSettingsMsg,
		'title': ccmi18n.user_group_add
		});
		$("#ccm-<?=$searchInstance?>-advanced-search").ajaxSubmit(function(r) {
		       ccm_parseAdvancedSearchResponse(r, '<?=$searchInstance?>');
		});
	});
};
$(function() { 
	$(".select2-select").select2();
});
</script>
