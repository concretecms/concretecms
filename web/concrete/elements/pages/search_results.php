<? defined('C5_EXECUTE') or die("Access Denied."); ?> 
<?
if ($_REQUEST['searchDialog'] == 1) {
	$searchDialog = true;
}
if (!isset($sitemap_select_mode)) {
	if (isset($_REQUEST['sitemap_select_mode'])) {
		$sitemap_select_mode = $_REQUEST['sitemap_select_mode'];
	}
}

if (!isset($sitemap_select_callback)) {
	if (isset($_REQUEST['sitemap_select_callback'])) {
		$sitemap_select_callback = $_REQUEST['sitemap_select_callback'];
	}
}
if (isset($_REQUEST['searchInstance'])) {
	$searchInstance = $_REQUEST['searchInstance'];
}
?>

<div id="ccm-<?=$searchInstance?>-search-results" class="ccm-file-list">

<? if (!$searchDialog) { ?>

<div class="ccm-pane-body">

<? } ?>

<div id="ccm-list-wrapper"><a name="ccm-<?=$searchInstance?>-list-wrapper-anchor"></a>
	<div style="float: right; margin-bottom: 10px">
		<? $form = Loader::helper('form'); ?>

		<?=$form->label('ccm-' . $searchInstance . '-list-multiple-operations', t('With Selected'))?>
		<select id="ccm-<?=$searchInstance?>-list-multiple-operations" style="width: 120px; margin-left: 8px;" disabled>
			<option value="">**</option>
			<option value="properties"><?=t('Edit Properties')?></option>
			<option value="design"><?=t('Design')?></option>
			<option value="delete"><?=t('Delete')?></option>
		</select>	
		<a href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/pages/customize_search_columns?searchInstance=<?=$searchInstance?>" id="ccm-search-add-column"><span class="ccm-menu-icon ccm-icon-properties"></span><?=t('Customize Results')?></a>
	</div>

<?
	$pageList->displaySummary();
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
			<th><?=t('Type')?></th>

			<th class="ccm-page-list-name <?=$pageList->getSearchResultsClass('cvName')?>"><a href="<?=$pageList->getSortByURL('cvName', 'asc', $bu, $soargs)?>"><?=t('Name')?></a></th>
			<th class="<?=$pageList->getSearchResultsClass('cvDatePublic')?>"><a href="<?=$pageList->getSortByURL('cvDatePublic', 'asc', $bu, $soargs)?>"><?=t('Public Date')?></a></th>
			<th class="<?=$pageList->getSearchResultsClass('cDateModified')?>"><a href="<?=$pageList->getSortByURL('cDateModified', 'asc', $bu, $soargs)?>"><?=t('Date Modified')?></a></th>
			<th><?=t('Owner')?></th>
			<? if ($pageList->isIndexedSearch()) { ?>
				<th class="<?=$pageList->getSearchResultsClass('cIndexScore')?>"><a href="<?=$pageList->getSortByURL('cIndexScore', 'desc', $bu, $soargs)?>"><?=t('Score')?></a></th>
			<? } ?>

			<? 
			$slist = CollectionAttributeKey::getColumnHeaderList();
			foreach($slist as $ak) { ?>
				<th class="<?=$pageList->getSearchResultsClass($ak)?>"><a href="<?=$pageList->getSortByURL($ak, 'asc', $bu, $soargs)?>"><?=$ak->getAttributeKeyDisplayHandle()?></a></th>
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
			<tr class="ccm-list-record <?=$striped?>" cName="<?=htmlentities($cobj->getCollectionName(), ENT_QUOTES, APP_CHARSET)?>" cID="<?=$cobj->getCollectionID()?>" sitemap-select-callback="<?=$sitemap_select_callback?>" sitemap-select-mode="<?=$sitemap_select_mode?>" sitemap-display-mode="search" canWrite="<?=$cpobj->canWrite()?>" cNumChildren="<?=$cobj->getNumChildren()?>" cAlias="false">
			<? if (!$searchDialog) { ?><td class="ccm-<?=$searchInstance?>-list-cb" style="vertical-align: middle !important"><input type="checkbox" value="<?=$cobj->getCollectionID()?>" /></td><? } ?>
			<td><?=$cobj->getCollectionTypeName()?></td>
			<td class="ccm-page-list-name"><div style="max-width: 150px; word-wrap: break-word"><?=$txt->highlightSearch($cobj->getCollectionName(), $keywords)?></div></td>
			<td><?=date(DATE_APP_DASHBOARD_SEARCH_RESULTS_PAGES, strtotime($cobj->getCollectionDatePublic()))?></td>
			<td><?=date(DATE_APP_DASHBOARD_SEARCH_RESULTS_PAGES, strtotime($cobj->getCollectionDateLastModified()))?></td>
			<td><?
				$ui = UserInfo::getByID($cobj->getCollectionUserID());
				if (is_object($ui)) {
					print $ui->getUserName();
				}
			?></td>
			<? if ($pageList->isIndexedSearch()) { ?>
				<td><?=$cobj->getPageIndexScore()?></td>
			<? } ?>
			
			<? 
			$slist = CollectionAttributeKey::getColumnHeaderList();
			foreach($slist as $ak) { ?>
				<td><?
				$vo = $cobj->getAttributeValueObject($ak);
				if (is_object($vo)) {
					print $vo->getValue('display');
				}
				?></td>
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

<? if (!$searchDialog) { ?>
</div>

<div class="ccm-pane-footer">
	<? 	$pageList->displayPaging($bu, false, $soargs); ?>
</div>

<? } else { ?>
	<div class="ccm-pane-dialog-pagination">
		<? 	$pageList->displayPaging($bu, false, $soargs); ?>
	</div>
<? } ?>

</div>
