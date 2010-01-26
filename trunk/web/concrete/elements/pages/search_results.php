<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?> 
<?
if (isset($_REQUEST['searchDialog'])) {
	$searchDialog = true;
}
?>

<div id="ccm-list-wrapper"><a name="ccm-page-list-wrapper-anchor"></a>

<? if (!$searchDialog) { ?>

<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
<td width="100%"><?=$pageList->displaySummary();?></td>
	<td style="white-space: nowrap"><?=t('With Selected: ')?>&nbsp;</td>
	<td align="right">
	<select id="ccm-page-list-multiple-operations" disabled>
		<option value="">**</option>
		<option value="properties"><?=t('Edit Properties')?></option>
	</select>
	</td>
</tr>
</table>
<? } ?>
<?
	$txt = Loader::helper('text');
	$keywords = $searchRequest['keywords'];
	$bu = REL_DIR_FILES_TOOLS_REQUIRED . '/pages/search_results';
	
	if (count($pages) > 0) { ?>	
		<table border="0" cellspacing="0" cellpadding="0" id="ccm-page-list" class="ccm-results-list">
		<tr>
			<? if (!$searchDialog) { ?><th><input id="ccm-page-list-cb-all" type="checkbox" /></th><? } ?>
			<th><?=t('Type')?></th>

			<th class="ccm-page-list-name <?=$pageList->getSearchResultsClass('cvName')?>"><a href="<?=$pageList->getSortByURL('cvName', 'asc', $bu)?>"><?=t('Name')?></a></th>
			<th class="<?=$pageList->getSearchResultsClass('cvDatePublic')?>"><a href="<?=$pageList->getSortByURL('cvDatePublic', 'asc', $bu)?>"><?=t('Public Date')?></a></th>
			<th class="<?=$pageList->getSearchResultsClass('cDateModified')?>"><a href="<?=$pageList->getSortByURL('cDateModified', 'asc', $bu)?>"><?=t('Date Modified')?></a></th>
			<th><?=t('Owner')?></th>
			<? 
			$slist = CollectionAttributeKey::getColumnHeaderList();
			foreach($slist as $ak) { ?>
				<th class="<?=$pageList->getSearchResultsClass($ak)?>"><a href="<?=$pageList->getSortByURL($ak, 'asc', $bu)?>"><?=$ak->getAttributeKeyDisplayHandle()?></a></th>
			<? } ?>			
			<th class="ccm-search-add-column-header"><a href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/pages/customize_search_columns" id="ccm-search-add-column"><img src="<?=ASSETS_URL_IMAGES?>/icons/add.png" width="16" height="16" /></a></th>
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
			<tr class="ccm-list-record <?=$striped?>" chooseOnly="<?=$searchDialog?>" cName="<?=htmlentities($cobj->getCollectionName(), ENT_QUOTES, APP_CHARSET)?>" cID="<?=$cobj->getCollectionID()?>" sitemap-mode="search" canWrite="<?=$cpobj->canWrite()?>" cNumChildren="<?=$cobj->getNumChildren()?>" cAlias="false">
			<? if (!$searchDialog) { ?><td class="ccm-page-list-cb" style="vertical-align: middle !important"><input type="checkbox" value="<?=$cobj->getCollectionID()?>" /></td><? } ?>
			<td><?=$cobj->getCollectionTypeName()?></td>
			<td class="ccm-page-list-name"><?=$txt->highlightSearch(wordwrap($cobj->getCollectionName(), 15, "\n", true), $keywords)?></td>
			<td><?=date('M d, Y g:ia', strtotime($cobj->getCollectionDatePublic()))?></td>
			<td><?=date('M d, Y g:ia', strtotime($cobj->getCollectionDateLastModified()))?></td>
			<td><?
				$ui = UserInfo::getByID($cobj->getCollectionUserID());
				print $ui->getUserName();
			?></td>
			
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
			<td>&nbsp;</td>
			
			</tr>
			<?
		}

	?>
	
	</table>
	
	

	<? } else { ?>
		
		<div class="ccm-results-list-none"><?=t('No pages found.')?></div>
		
	
	<? } 
	$pageList->displayPaging($bu); ?>
	
</div>