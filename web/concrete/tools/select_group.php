<?

defined('C5_EXECUTE') or die("Access Denied.");

$tp = new TaskPermission();
if (!$tp->canAccessGroupSearch()) { 
	echo(t("You have no access to groups."));
} else { 	
	
	if (!$_REQUEST['group_submit_search']) { ?>
	<div id="ccm-group-search-wrapper">
	<? } ?>
	
	<? 
	Loader::model('search/group');
	$gl = new GroupSearch();
	if ($_REQUEST['include_core_groups'] == 1) {
		$gl->includeAllGroups();
	}
	if (isset($_GET['gKeywords'])) {
		$gl->filterByKeywords($_GET['gKeywords']);
	}
	
	$gl->updateItemsPerPage(8);
	
	$gResults = $gl->getPage();
	
	?>
	
	<?php
	$group_search_form = '
		<form id="ccm-group-search" style=""  method="get" action="'. REL_DIR_FILES_TOOLS_REQUIRED .'/select_group/">
		<div id="ccm-group-search-fields">
		<input type="text" id="ccm-group-search-keywords" name="gKeywords" value="'. $_REQUEST['gKeywords'] .'" class="ccm-text" style="width: 100px" />
		<input type="submit" value="'. t('Search') .'" />
		<input type="hidden" name="group_submit_search" value="1" />
		<input type="hidden" name="include_core_groups" value="' . $_REQUEST['include_core_groups'] . '" />
		</div>
		</form>
	';
	?>
	
	<? if (count($gResults) > 0) { 
	
		$gl->displaySummary( $group_search_form );
	
		foreach ($gResults as $g) { ?>
	
		<div class="ccm-group">
			<div style="background-image: url(<?=ASSETS_URL_IMAGES?>/icons/group.png)" class="ccm-group-inner-indiv">
				<a class="ccm-group-inner-atag" id="g<?=$g['gID']?>" group-id="<?=$g['gID']?>" group-name="<?=$g['gName']?>" href="javascript:void(0)"><?=$g['gName']?></a>
				<?=( $g['gDescription'] != '' ? ' - <span class="ccm-group-description">'. $g['gDescription'] .'</span>' : '' )?>
			</div>
		</div>
	
	<? } ?>
	
	<div id="ccm-group-paging">
	<?
	$url = REL_DIR_FILES_TOOLS_REQUIRED . '/select_group?gKeywords=' . $_REQUEST['gKeywords'] . '&include_core_groups=' . $_REQUEST['include_core_groups'] . '&' . PAGING_STRING . '=%pageNum%';
	$gl->displayPaging($url);
	?>
	</div>
	
	<?
	
	} else { ?>
	
		<p><?=t('No groups found.')?></p>
		
	<? } ?>
	
	<? if (!$_REQUEST['group_submit_search']) { ?>
	
	</div>
	
	
	<? } ?>
	
	<script type="text/javascript">
	head.ready(function() {
		ccm_setupGroupSearch();
	});
	</script>
<? } ?>