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
	if ($_REQUEST['filter'] == 'assign') {
		$pk = PermissionKey::getByHandle('assign_user_groups');
		$gl->filterByAllowedPermission($pk);
	}
	if (isset($_GET['gKeywords'])) {
		$gl->filterByKeywords($_GET['gKeywords']);
	}

	$gl->updateItemsPerPage(20);
	
	$gResults = $gl->getPage();

	?>

	<script type="text/javascript">ccm_addHeaderItem("<?=Loader::helper('html')->css('dynatree/dynatree.css')->href;?>", "CSS");</script>
	<script type="text/javascript">ccm_addHeaderItem("<?=Loader::helper('html')->javascript('dynatree/dynatree.js')->href;?>", "JAVASCRIPT");</script>
	

	<div class="ccm-pane-options">
	<div class="ccm-pane-options-permanent-search">
		<form id="ccm-group-search" style=""  method="get" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/select_group/">
		<div id="ccm-group-search-fields" class="ccm-ui">
		
		<input type="text" id="ccm-group-search-keywords" name="gKeywords" value="<?=htmlspecialchars($_REQUEST['gKeywords'])?>" class="ccm-text" style="width: 100px" />
		<input type="submit" value="<?=t('Search')?>" class="btn" />
		<input type="hidden" name="group_submit_search" value="1" />
		<input type="hidden" name="callback" value="<?=htmlspecialchars($_REQUEST['callback'])?>" />
		<input type="hidden" name="include_core_groups" value="<?=htmlspecialchars($_REQUEST['include_core_groups'])?>" />
		</div>
		</form>
	</div>
	</div>

	<div id="ccm-list-wrapper">

	<? if ($_REQUEST['group_submit_search']) { ?>
		<? if (count($gResults) > 0) { 
		
			print $gl->displaySummary();
		
			foreach ($gResults as $gRow) {
				$g = Group::getByID($gRow['gID']);
				 ?>
		
			<div class="ccm-group">
				<div style="background-image: url(<?=ASSETS_URL_IMAGES?>/icons/group.png)" class="ccm-group-inner-indiv">
					<a class="ccm-group-inner-atag" id="g<?=$g->getGroupID()?>" group-id="<?=$g->getGroupID()?>" group-name="<?=$g->getGroupDisplayName(false)?>" href="javascript:void(0)"><?=$g->getGroupDisplayName()?></a>
					<?=( $g->getGroupDescription() != '' ? ' - <span class="ccm-group-description">'. $g->getGroupDescription() .'</span>' : '' )?>
				</div>
			</div>
			
			<? }

		} else { ?>
		
			<p><?=t('No groups found.')?></p>
		
		<? } ?>

	<? } else { ?>

		<? $tree = GroupTree::get();?>
		<? if (is_object($tree)) { 
			$callback = h($_REQUEST['callback']);
			?>
			<div class="group-tree" data-group-tree="<?=$tree->getTreeID()?>">
			</div>
			<script type="text/javascript">
			$(function() {
				$('[data-group-tree]').ccmgroupstree({
					'treeID': '<?=$tree->getTreeID()?>',
					'disableDragAndDrop': true,
					'onClick': function(e, node) {
						<? if ($callback) { ?>
							func = window['<?=$callback?>'];
						<? } else { ?> 
							func = ccm_triggerSelectGroup;
						<? } ?>
						func(node.data.gID, node.data.title);
						jQuery.fn.dialog.closeTop();
					}
				});
			});
			</script>
		<? } ?>
	<? } ?>
	
	</div>


	<? if ($_REQUEST['group_submit_search']) { ?>

	<div id="ccm-group-paging" class="ccm-pane-dialog-pagination">
	<?
	$url = REL_DIR_FILES_TOOLS_REQUIRED . '/select_group?group_submit_search=1&callback=' . htmlspecialchars($_REQUEST['callback']) . '&gKeywords=' . htmlspecialchars($_REQUEST['gKeywords']) . '&include_core_groups=' . htmlspecialchars($_REQUEST['include_core_groups']) . '&' . PAGING_STRING . '=%pageNum%';
	$gl->displayPagingV2($url);
	?>
	</div>

	<? } ?>
	

	<? if (!$_REQUEST['group_submit_search']) { ?>
	
	</div>
	
	<? } ?>
	
	<script type="text/javascript">
	$(function() {
		ccm_setupGroupSearch('<?=htmlspecialchars($_REQUEST['callback'])?>');
	});
	</script>

<? } ?>