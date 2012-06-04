<? defined('C5_EXECUTE') or die("Access Denied."); ?> 
<?
if ($_REQUEST['searchDialog'] == 1) {
	$searchDialog = true;
}
if (!isset($sitemap_select_mode)) {
	if (isset($_REQUEST['sitemap_select_mode'])) {
		$sitemap_select_mode = Loader::helper('text')->entities($_REQUEST['$sitemap_select_mode']);
	}
}

if (!isset($sitemap_select_callback)) {
	if (isset($_REQUEST['sitemap_select_callback'])) {
		$sitemap_select_callback = Loader::helper('text')->entities($_REQUEST['sitemap_select_callback']);
	}
}
if (isset($_REQUEST['searchInstance'])) {
	$searchInstance = Loader::helper('text')->entities($_REQUEST['searchInstance']);
}
?>

<div id="ccm-<?=$searchInstance?>-search-results" class="ccm-file-list">

<? if (!$searchDialog) { ?>

<div class="ccm-pane-body">

<? } ?>

<div id="ccm-list-wrapper"><a name="ccm-<?=$searchInstance?>-list-wrapper-anchor"></a>
	<div style="margin-bottom: 10px">
		<? $form = Loader::helper('form'); ?>

		<select id="ccm-<?=$searchInstance?>-list-multiple-operations" class="span3" disabled>
			<option value="">** <?=t('With Selected')?></option>
			<option value="properties"><?=t('Edit Properties')?></option>
			<option value="move_copy"><?=t('Move/Copy')?></option>
			<option value="speed_settings"><?=t('Speed Settings')?></option>
			<option value="design"><?=t('Design')?></option>
			<option value="delete"><?=t('Delete')?></option>
		</select>	
	</div>

<?
	$txt = Loader::helper('text');
	$keywords = $searchRequest['keywords'];
	$soargs = array();
	$soargs['searchInstance'] = $searchInstance;
	$soargs['sitemap_select_mode'] = $sitemap_select_mode;
	$soargs['sitemap_select_callback'] = $sitemap_select_callback;
	$soargs['searchDialog'] = $searchDialog;
	$bu = REL_DIR_FILES_TOOLS_REQUIRED . '/pages/search_results';
	
	if (count($pages) > 0) { ?>	
		<table border="0" cellspacing="0" cellpadding="0" id="ccm-<?=$searchInstance?>-list" class="ccm-results-list">
		<tr>
			<? if (!$searchDialog) { ?><th><input id="ccm-<?=$searchInstance?>-list-cb-all" type="checkbox" /></th><? } ?>
			<? if ($pageList->isIndexedSearch()) { ?>
				<th class="<?=$pageList->getSearchResultsClass('cIndexScore')?>"><a href="<?=$pageList->getSortByURL('cIndexScore', 'desc', $bu, $soargs)?>"><?=t('Score')?></a></th>
			<? } ?>
			<? foreach($columns->getColumns() as $col) { ?>
				<? if ($col->isColumnSortable()) { ?>
					<th class="<?=$pageList->getSearchResultsClass($col->getColumnKey())?>"><a href="<?=$pageList->getSortByURL($col->getColumnKey(), $col->getColumnDefaultSortDirection(), $bu, $soargs)?>"><?=$col->getColumnName()?></a></th>
				<? } else { ?>
					<th><?=$col->getColumnName()?></th>
				<? } ?>
			<? } ?>

		</tr>
	<?
		foreach($pages as $cobj) {
			$cpobj = new Permissions($cobj); 
			if (!isset($striped) || $striped == 'ccm-list-record-alt') {
				$striped = '';
			} else if ($striped == '') { 
				$striped = 'ccm-list-record-alt';
			}

			?>
			<tr class="ccm-list-record <?=$striped?>" cName="<?=htmlentities($cobj->getCollectionName(), ENT_QUOTES, APP_CHARSET)?>" cID="<?=$cobj->getCollectionID()?>" sitemap-select-callback="<?=$sitemap_select_callback?>" sitemap-select-mode="<?=$sitemap_select_mode?>" sitemap-instance-id="<?=$searchInstance?>" sitemap-display-mode="search" canWrite="<?=$cpobj->canWrite()?>" cNumChildren="<?=$cobj->getNumChildren()?>" cAlias="false">
			<? if (!$searchDialog) { ?><td class="ccm-<?=$searchInstance?>-list-cb" style="vertical-align: middle !important"><input type="checkbox" value="<?=$cobj->getCollectionID()?>" /></td><? } ?>



			<? foreach($columns->getColumns() as $col) { ?>
				<? if ($col->getColumnKey() == 'cvName') { ?>
					<td class="ccm-page-list-name"><?=$txt->highlightSearch($cobj->getCollectionName(), $keywords)?></td>		
				<? } else { ?>
					<td><?=$col->getColumnValue($cobj)?></td>
				<? } ?>
			<? } ?>

			</tr>
			<?
		}
	?>
	
	</table>
	
	

	<? } else { ?>
		
		<div class="ccm-results-list-none"><?=t('No pages found.')?></div>
		
	
	<? } ?>
	
</div>
<?
	$pageList->displaySummary();
?>
<? if (!$searchDialog) { ?>
</div>

<div class="ccm-pane-footer">
	<? 	$pageList->displayPagingV2($bu, false, $soargs); ?>
</div>

<? } else { ?>
	<div class="ccm-pane-dialog-pagination">
		<? 	$pageList->displayPagingV2($bu, false, $soargs); ?>
	</div>
<? } ?>

</div>
