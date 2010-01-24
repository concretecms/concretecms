<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?> 

<div id="ccm-list-wrapper"><a name="ccm-file-list-wrapper-anchor"></a>
<?
	$pageList->displaySummary();
	$txt = Loader::helper('text');
	$keywords = $searchRequest['keywords'];
	$bu = REL_DIR_FILES_TOOLS_REQUIRED . '/pages/search_results';
	
	if (count($pages) > 0) { ?>	
		<table border="0" cellspacing="0" cellpadding="0" id="ccm-file-list" class="ccm-results-list">
		<tr>
			<th><input id="ccm-file-list-cb-all" type="checkbox" /></td>
			<th><select id="ccm-file-list-multiple-operations" disabled>
				<option value="">**</option>
				<option value="download"><?=t('Download')?></option>
				<option value="sets"><?=t('Sets')?></option>
				<option value="properties"><?=t('Properties')?></option>
				<option value="rescan"><?=t('Rescan')?></option>
				<option value="delete"><?=t('Delete')?></option>
			</select>
			</th>
			<th>Type</th>

			<th class="ccm-page-list-name <?=$pageList->getSearchResultsClass('cvName')?>"><a href="<?=$pageList->getSortByURL('cvName', 'asc', $bu)?>"><?=t('Name')?></a></th>
			<th class="<?=$pageList->getSearchResultsClass('cvDatePublic')?>"><a href="<?=$pageList->getSortByURL('cvDatePublic', 'asc', $bu)?>"><?=t('Public Date')?></a></th>
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
			<tr class="ccm-list-record <?=$striped?>">
			<td class="ccm-file-list-cb" style="vertical-align: middle !important"><input type="checkbox" value="<?=$cobj->getCollectionID()?>" /></td>
			<td>der</td>
			<td><?=$cobj->getCollectionTypeID()?></td>
			<td class="ccm-page-list-name"><?=$txt->highlightSearch(wordwrap($cobj->getCollectionName(), 15, "\n", true), $keywords)?></td>
			<td><?=date('M d, Y g:ia', strtotime($cobj->getCollectionDatePublic()))?></td>
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