<?

defined('C5_EXECUTE') or die("Access Denied.");

$tp = new TaskPermission();
if (!$tp->canAccessGroupSearch()) { 
	echo(t("You have no access to groups."));
} else { 	

	if ($_REQUEST['filter'] == 'assign') { 
		$pk = PermissionKey::getByHandle('assign_user_groups');
		if (!$pk->validate()) {
			die(t('You have no access to assign groups.'));
		}
	}
	
	if (!$_REQUEST['group_submit_search']) { ?>
	<div id="ccm-group-search-wrapper">
	<? } ?>
	
	<? 
	Loader::model('search/group');
	$gl = new GroupSearch();
	if ($_REQUEST['include_core_groups'] == 1) {
		$gl->includeAllGroups();
	}
	if ($_REQUEST['filter'] == 'assign') {
		$gl->filterByAllowedPermission($pk);
	}
	if (isset($_GET['gKeywords'])) {
		$gl->filterByKeywords($_GET['gKeywords']);
	}
	
	$gl->updateItemsPerPage(8);
	
	$gResults = $gl->getPage();
	
	?>

	<div class="ccm-pane-options">
	<div class="ccm-pane-options-permanent-search">
		<form id="ccm-group-search" style=""  method="get" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/select_group/">
		<div id="ccm-group-search-fields" class="ccm-ui">
		
		<input type="text" id="ccm-group-search-keywords" name="gKeywords" value="<?=h($_REQUEST['gKeywords'])?>" class="ccm-text" style="width: 100px" />
		<input type="submit" value="<?=t('Search')?>" class="btn" />
		<input type="hidden" name="group_submit_search" value="1" />
		<input type="hidden" name="callback" value="<?=h($_REQUEST['callback'])?>" />
		<input type="hidden" name="include_core_groups" value="<?=h($_REQUEST['include_core_groups'])?>" />
		</div>
		</form>
	</div>
	</div>
	
	<div id="ccm-list-wrapper">
	
	<? if (count($gResults) > 0) { 
	
		print $gl->displaySummary();
	
		foreach ($gResults as $g) { ?>
	
		<div class="ccm-group">
			<div style="background-image: url(<?=ASSETS_URL_IMAGES?>/icons/group.png)" class="ccm-group-inner-indiv">
				<a class="ccm-group-inner-atag" id="g<?=$g['gID']?>" group-id="<?=$g['gID']?>" group-name="<?=h(tc('GroupName', $g['gName']))?>" href="javascript:void(0)"><?=h(tc('GroupName', $g['gName']))?></a>
				<?=( tc('GroupDescription', $g['gDescription']) != '' ? ' - <span class="ccm-group-description">'. h(tc('GroupDescription', $g['gDescription'])) .'</span>' : '' )?>
			</div>
		</div>
	
	<? } ?>
	
	<div id="ccm-group-paging" class="ccm-pane-dialog-pagination">
	<?
	$url = REL_DIR_FILES_TOOLS_REQUIRED . '/select_group?callback=' . h($_REQUEST['callback']) . '&gKeywords=' . h($_REQUEST['gKeywords']) . '&include_core_groups=' . h($_REQUEST['include_core_groups']) . '&' . PAGING_STRING . '=%pageNum%';
	$gl->displayPagingV2($url);
	?>
	</div>
	
	<?
	
	} else { ?>
	
		<p><?=t('No groups found.')?></p>
		
	<? } ?>
	
	</div>
	
	<? if (!$_REQUEST['group_submit_search']) { ?>
	
	</div>
	
	
	<? } ?>
	
	<script type="text/javascript">
	$(function() {
		ccm_setupGroupSearch('<?=h($_REQUEST['callback'])?>');
	});
	</script>
<? } ?>