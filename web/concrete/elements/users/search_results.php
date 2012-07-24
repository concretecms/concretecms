<? defined('C5_EXECUTE') or die("Access Denied."); ?> 

<div id="ccm-user-search-results">

<? if ($searchType == 'DASHBOARD') { ?>

<div class="ccm-pane-body">

<? } 

$ek = PermissionKey::getByHandle('edit_user_properties');
$ik = PermissionKey::getByHandle('activate_user');
$gk = PermissionKey::getByHandle('assign_user_groups');
$dk = PermissionKey::getByHandle('delete_user');

if (!$mode) {
		$mode = $_REQUEST['mode'];
	}
	if (!$searchType) {
		$searchType = $_REQUEST['searchType'];
	}
	
	$soargs = array();
	$soargs['searchType'] = $searchType;
	$soargs['mode'] = $mode;
	$searchInstance = 'user';

	?>

<div id="ccm-list-wrapper"><a name="ccm-<?=$searchInstance?>-list-wrapper-anchor"></a>

	<div style="margin-bottom: 10px">
		<? $form = Loader::helper('form'); ?>

		<a href="<?=View::url('/dashboard/users/add')?>" style="float: right" class="btn primary"><?=t("Add User")?></a>
		<select id="ccm-<?=$searchInstance?>-list-multiple-operations" class="span3" disabled>
					<option value="">** <?=t('With Selected')?></option>
					<? if ($ek->validate()) { ?>
						<option value="properties"><?=t('Edit Properties')?></option>
					<? } ?>
					<? if ($ik->validate()) { ?>
						<option value="activate"><?=t('Activate')?></option>
						<option value="deactivate"><?=t('Deactivate')?></option>
					<? } ?>
					<? if ($gk->validate()) { ?>
					<option value="group_add"><?=t('Add to Group')?></option>
					<option value="group_remove"><?=t('Remove from Group')?></option>
					<? } ?>
					<? if ($dk->validate()) { ?>
					<option value="delete"><?=t('Delete')?></option>
					<? } ?>
				<? if ($mode == 'choose_multiple') { ?>
					<option value="choose"><?=t('Choose')?></option>
				<? } ?>
				</select>

	</div>

	<?
	$txt = Loader::helper('text');
	$keywords = $_REQUEST['keywords'];
	$bu = REL_DIR_FILES_TOOLS_REQUIRED . '/users/search_results';
	
	if (count($users) > 0) { ?>	
		<table border="0" cellspacing="0" cellpadding="0" id="ccm-user-list" class="ccm-results-list">
		<tr>
			<th width="1"><input id="ccm-user-list-cb-all" type="checkbox" /></th>
			<? foreach($columns->getColumns() as $col) { ?>
				<? if ($col->isColumnSortable()) { ?>
					<th class="<?=$userList->getSearchResultsClass($col->getColumnKey())?>"><a href="<?=$userList->getSortByURL($col->getColumnKey(), $col->getColumnDefaultSortDirection(), $bu, $soargs)?>"><?=$col->getColumnName()?></a></th>
				<? } else { ?>
					<th><?=$col->getColumnName()?></th>
				<? } ?>
			<? } ?>

		</tr>
	<?
		foreach($users as $ui) { 
			$action = View::url('/dashboard/users/search?uID=' . $ui->getUserID());
			
			if ($mode == 'choose_one' || $mode == 'choose_multiple') {
				$action = 'javascript:void(0); ccm_triggerSelectUser(' . $ui->getUserID() . ',\'' . $txt->entities($ui->getUserName()) . '\',\'' . $txt->entities($ui->getUserEmail()) . '\'); jQuery.fn.dialog.closeTop();';
			}
			
			if (!isset($striped) || $striped == 'ccm-list-record-alt') {
				$striped = '';
			} else if ($striped == '') { 
				$striped = 'ccm-list-record-alt';
			}

			?>
		
			<tr class="ccm-list-record <?=$striped?>">
			<td class="ccm-user-list-cb" style="vertical-align: middle !important"><input type="checkbox" value="<?=$ui->getUserID()?>" user-email="<?=$ui->getUserEmail()?>" user-name="<?=$ui->getUserName()?>" /></td>
			<? foreach($columns->getColumns() as $col) { ?>
				<? if ($col->getColumnKey() == 'uName') { ?>
					<td><a href="<?=$action?>"><?=$ui->getUserName()?></a></td>
				<? } else { ?>
					<td><?=$col->getColumnValue($ui)?></td>
				<? } ?>
			<? } ?>

			</tr>
			<?
		}

	?>
	
	</table>
	
	

	<? } else { ?>
		
		<div id="ccm-list-none"><?=t('No users found.')?></div>
		
	
	<? }  ?>

</div>

<div id="ccm-export-results-wrapper">
	<a id="ccm-export-results" href="javascript:void(0)" onclick="$('#ccm-user-advanced-search').attr('action', '<?=REL_DIR_FILES_TOOLS_REQUIRED?>/users/search_results_export'); $('#ccm-user-advanced-search').get(0).submit(); $('#ccm-user-advanced-search').attr('action', '<?=REL_DIR_FILES_TOOLS_REQUIRED?>/users/search_results');"><span></span><?=t('Export')?></a>
</div>

<?
	$userList->displaySummary();
?>

<? if ($searchType == 'DASHBOARD') { ?>
</div>

<div class="ccm-pane-footer">
	<? 	$userList->displayPagingV2($bu, false, $soargs); ?>
</div>

<? } else { ?>
	<div class="ccm-pane-dialog-pagination">
		<? 	$userList->displayPagingV2($bu, false, $soargs); ?>
	</div>
<? } ?>

</div>

<script type="text/javascript">
$(function() { 
	ccm_setupUserSearch('<?=$searchInstance?>'); 
});
</script>