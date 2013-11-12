<? defined('C5_EXECUTE') or die("Access Denied."); ?> 


<? if (count($pages) > 0) { 
	$txt = Loader::helper('text');
	$keywords = $searchRequest['keywords'];
	$soargs = array();
	$bu = REL_DIR_FILES_TOOLS_REQUIRED . '/pages/search_results';

	?>
	<table border="0" cellspacing="0" cellpadding="0" class="ccm-search-results-table">
	<thead>
		<tr>
			<th><span class="ccm-search-results-checkbox"><input type="checkbox" data-search-checkbox="select-all" /></span></th>
			<? foreach($columns->getColumns() as $col) { ?>
				<? if ($pageList->isIndexedSearch()) { ?>
					<th class="<?=$pageList->getSearchResultsClass('cIndexScore')?>"><a href="<?=$pageList->getSortByURL('cIndexScore', 'desc', $bu, $soargs)?>"><?=t('Score')?></a></th>
				<? } ?>
				<? if ($col->isColumnSortable()) { ?>
					<th class="<?=$pageList->getSearchResultsClass($col->getColumnKey())?>"><a href="<?=$pageList->getSortByURL($col->getColumnKey(), $col->getColumnDefaultSortDirection(), $bu, $soargs)?>"><?=$col->getColumnName()?></a></th>
				<? } else { ?>
					<th><span><?=$col->getColumnName()?></span></th>
				<? } ?>
			<? } ?>
		</tr>
	</thead>
	<tbody>
	<?
		$h = Loader::helper('concrete/dashboard');
		$dsh = Loader::helper('concrete/dashboard/sitemap');
		foreach($pages as $cobj) {
			$cpobj = new Permissions($cobj); 
			$canEditPageProperties = $cpobj->canEditPageProperties();
			$canEditPageSpeedSettings = $cpobj->canEditPageSpeedSettings();
			$canEditPagePermissions = $cpobj->canEditPagePermissions();
			$canEditPageDesign = ($cpobj->canEditPageTheme() || $cpobj->canEditPageTemplate());
			$canViewPageVersions = $cpobj->canViewPageVersions();
			$canDeletePage = $cpobj->canDeletePage();
			$canAddSubpages = $cpobj->canAddSubpage();
			$canAddExternalLinks = $cpobj->canAddExternalLink();

			$permissionArray = array(
				'canEditPageProperties'=> $canEditPageProperties,
				'canEditPageSpeedSettings'=>$canEditPageSpeedSettings,
				'canEditPagePermissions'=>$canEditPagePermissions,
				'canEditPageDesign'=>$canEditPageDesign,
				'canViewPageVersions'=>$canViewPageVersions,
				'canDeletePage'=>$canDeletePage,
				'canAddSubpages'=>$canAddSubpages,
				'canAddExternalLinks'=>$canAddExternalLinks,
				'cName' => Loader::helper('text')->entities($cobj->getCollectionName()),
				'cID' => $cobj->getCollectionID(),
				'cNumChildren' => $cobj->getNumChildren(),
				'cAlias' => false
			);

			
			?>
			<tr>
			<td><span class="ccm-search-results-checkbox"><input type="checkbox" data-search-checkbox="individual" value="<?=$cobj->getCollectionID()?>" /></span></td>
			<?php if ($pageList->isIndexedSearch()){?>
			<td><?= $cobj->getPageIndexScore();?></td>
			<?php } ?>
			<? foreach($columns->getColumns() as $col) { ?>
				<? if ($col->getColumnKey() == 'cvName') { ?>
					<td class="ccm-search-results-name"><?=$txt->highlightSearch($cobj->getCollectionName(), $keywords)?></td>		
				<? } else { ?>
					<td><?=$col->getColumnValue($cobj)?></td>
				<? } ?>
			<? } ?>

			</tr>
			<?
		}
	?>
	</tbody>
	</table>
	

	<? } else { ?>
		
		<div><?=t('No pages found.')?></div>
	
	<? } ?>
	
<? $pageList->displayPagingV2($bu, false, $soargs); ?>
