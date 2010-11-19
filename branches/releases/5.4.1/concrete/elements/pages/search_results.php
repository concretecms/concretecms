<?php  defined('C5_EXECUTE') or die("Access Denied."); ?> 
<?php 
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

<div id="ccm-list-wrapper"><a name="ccm-<?php echo $searchInstance?>-list-wrapper-anchor"></a>

<?php  if (!$searchDialog) { ?>

<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
<td width="100%"><?php echo $pageList->displaySummary();?></td>
	<td style="white-space: nowrap"><?php echo t('With Selected: ')?>&nbsp;</td>
	<td align="right">
	<select id="ccm-<?php echo $searchInstance?>-list-multiple-operations" disabled>
		<option value="">**</option>
		<option value="properties"><?php echo t('Edit Properties')?></option>
	</select>
	</td>
</tr>
</table>
<?php  } ?>
<?php 
	$txt = Loader::helper('text');
	$keywords = $searchRequest['keywords'];
	$soargs = array();
	$soargs['searchInstance'] = $searchInstance;
	$soargs['sitemap_select_mode'] = $sitemap_select_mode;
	$soargs['sitemap_select_callback'] = $sitemap_select_callback;
	$soargs['searchDialog'] = $searchDialog;
	$bu = REL_DIR_FILES_TOOLS_REQUIRED . '/pages/search_results';
	
	if (count($pages) > 0) { ?>	
		<table border="0" cellspacing="0" cellpadding="0" id="ccm-<?php echo $searchInstance?>-list" class="ccm-results-list">
		<tr class="ccm-results-list-header">
			<?php  if (!$searchDialog) { ?><th><input id="ccm-<?php echo $searchInstance?>-list-cb-all" type="checkbox" /></th><?php  } ?>
			<th><?php echo t('Type')?></th>

			<th class="ccm-page-list-name <?php echo $pageList->getSearchResultsClass('cvName')?>"><a href="<?php echo $pageList->getSortByURL('cvName', 'asc', $bu, $soargs)?>"><?php echo t('Name')?></a></th>
			<th class="<?php echo $pageList->getSearchResultsClass('cvDatePublic')?>"><a href="<?php echo $pageList->getSortByURL('cvDatePublic', 'asc', $bu, $soargs)?>"><?php echo t('Public Date')?></a></th>
			<th class="<?php echo $pageList->getSearchResultsClass('cDateModified')?>"><a href="<?php echo $pageList->getSortByURL('cDateModified', 'asc', $bu, $soargs)?>"><?php echo t('Date Modified')?></a></th>
			<th><?php echo t('Owner')?></th>
			<?php  if ($pageList->isIndexedSearch()) { ?>
				<th class="<?php echo $pageList->getSearchResultsClass('cIndexScore')?>"><a href="<?php echo $pageList->getSortByURL('cIndexScore', 'desc', $bu, $soargs)?>"><?php echo t('Score')?></a></th>
			<?php  } ?>

			<?php  
			$slist = CollectionAttributeKey::getColumnHeaderList();
			foreach($slist as $ak) { ?>
				<th class="<?php echo $pageList->getSearchResultsClass($ak)?>"><a href="<?php echo $pageList->getSortByURL($ak, 'asc', $bu, $soargs)?>"><?php echo $ak->getAttributeKeyDisplayHandle()?></a></th>
			<?php  } ?>			
			<th class="ccm-search-add-column-header"><a href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/pages/customize_search_columns?searchInstance=<?php echo $searchInstance?>" id="ccm-search-add-column"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/add.png" width="16" height="16" /></a></th>
		</tr>
	<?php 
		foreach($pages as $cobj) {
			$cpobj = new Permissions($cobj); 
			if (!isset($striped) || $striped == 'ccm-list-record-alt') {
				$striped = '';
			} else if ($striped == '') { 
				$striped = 'ccm-list-record-alt';
			}

			?>
			<tr class="ccm-list-record <?php echo $striped?>" cName="<?php echo htmlentities($cobj->getCollectionName(), ENT_QUOTES, APP_CHARSET)?>" cID="<?php echo $cobj->getCollectionID()?>" sitemap-select-callback="<?php echo $sitemap_select_callback?>" sitemap-select-mode="<?php echo $sitemap_select_mode?>" sitemap-display-mode="search" canWrite="<?php echo $cpobj->canWrite()?>" cNumChildren="<?php echo $cobj->getNumChildren()?>" cAlias="false">
			<?php  if (!$searchDialog) { ?><td class="ccm-<?php echo $searchInstance?>-list-cb" style="vertical-align: middle !important"><input type="checkbox" value="<?php echo $cobj->getCollectionID()?>" /></td><?php  } ?>
			<td><?php echo $cobj->getCollectionTypeName()?></td>
			<td class="ccm-page-list-name"><div style="max-width: 150px; word-wrap: break-word"><?php echo $txt->highlightSearch($cobj->getCollectionName(), $keywords)?></div></td>
			<td><?php echo date(DATE_APP_DASHBOARD_SEARCH_RESULTS_PAGES, strtotime($cobj->getCollectionDatePublic()))?></td>
			<td><?php echo date(DATE_APP_DASHBOARD_SEARCH_RESULTS_PAGES, strtotime($cobj->getCollectionDateLastModified()))?></td>
			<td><?php 
				$ui = UserInfo::getByID($cobj->getCollectionUserID());
				if (is_object($ui)) {
					print $ui->getUserName();
				}
			?></td>
			<?php  if ($pageList->isIndexedSearch()) { ?>
				<td><?php echo $cobj->getPageIndexScore()?></td>
			<?php  } ?>
			
			<?php  
			$slist = CollectionAttributeKey::getColumnHeaderList();
			foreach($slist as $ak) { ?>
				<td><?php 
				$vo = $cobj->getAttributeValueObject($ak);
				if (is_object($vo)) {
					print $vo->getValue('display');
				}
				?></td>
			<?php  } ?>		
			<td>&nbsp;</td>
			
			</tr>
			<?php 
		}

	?>
	
	</table>
	
	

	<?php  } else { ?>
		
		<div class="ccm-results-list-none"><?php echo t('No pages found.')?></div>
		
	
	<?php  } 
	$pageList->displayPaging($bu, false, $soargs); ?>
	
</div>