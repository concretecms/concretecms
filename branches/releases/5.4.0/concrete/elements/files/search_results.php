<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); ?> 
<?php 
if (isset($_REQUEST['searchInstance'])) {
	$searchInstance = $_REQUEST['searchInstance'];
}
?>
<script type="text/javascript">
	var CCM_STAR_STATES = {
		'unstarred':'star_grey.png',
		'starred':'star_yellow.png'
	};
	var CCM_STAR_ACTION    = 'files/star.php';
</script>
<div id="ccm-list-wrapper"><a name="ccm-<?php echo $searchInstance?>-list-wrapper-anchor"></a>
<?php 
	$fileList->displaySummary();
	$txt = Loader::helper('text');
	$keywords = $searchRequest['fKeywords'];
	$soargs = array();
	$soargs['searchInstance'] = $searchInstance;

	/*
	if ($searchType == 'DASHBOARD') {
		$bu = false;
	} else {
		$bu = REL_DIR_FILES_TOOLS_REQUIRED . '/files/search_results';
	}
	*/
	
	$bu = REL_DIR_FILES_TOOLS_REQUIRED . '/files/search_results';
	
	if (count($files) > 0) { ?>	
		<table border="0" cellspacing="0" cellpadding="0" id="ccm-<?php echo $searchInstance?>-list" class="ccm-results-list">
		<tr>
			<th><input id="ccm-<?php echo $searchInstance?>-list-cb-all" type="checkbox" /></td>
			<th><select id="ccm-<?php echo $searchInstance?>-list-multiple-operations" disabled>
				<option value="">**</option>
				<option value="download"><?php echo t('Download')?></option>
				<option value="sets"><?php echo t('Sets')?></option>
				<option value="properties"><?php echo t('Properties')?></option>
				<option value="rescan"><?php echo t('Rescan')?></option>
				<option value="delete"><?php echo t('Delete')?></option>
			</select>
			</th>
			<th>Type</th>

			<th class="ccm-file-list-starred">&nbsp;</th>			
			<th class="ccm-file-list-filename <?php echo $fileList->getSearchResultsClass('fvTitle')?>"><a href="<?php echo $fileList->getSortByURL('fvTitle', 'asc', $bu, $soargs)?>"><?php echo t('Title')?></a></th>
			<th class="<?php echo $fileList->getSearchResultsClass('fDateAdded')?>"><a href="<?php echo $fileList->getSortByURL('fDateAdded', 'asc', $bu, $soargs)?>"><?php echo t('Added')?></a></th>
			<th class="<?php echo $fileList->getSearchResultsClass('fvDateAdded')?>"><a href="<?php echo $fileList->getSortByURL('fvDateAdded', 'asc', $bu, $soargs)?>"><?php echo t('Active')?></a></th>
			<th class="<?php echo $fileList->getSearchResultsClass('fvSize')?>"><a href="<?php echo $fileList->getSortByURL('fvSize', 'asc', $bu, $soargs)?>"><?php echo t('Size')?></a></th>
			<?php  
			$slist = FileAttributeKey::getColumnHeaderList();
			foreach($slist as $ak) { ?>
				<th class="<?php echo $fileList->getSearchResultsClass($ak)?>"><a href="<?php echo $fileList->getSortByURL($ak, 'asc', $bu, $soargs)?>"><?php echo $ak->getAttributeKeyDisplayHandle()?></a></th>
			<?php  } ?>			
			<th class="ccm-search-add-column-header"><a href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/files/customize_search_columns?searchInstance=<?php echo $searchInstance?>" id="ccm-search-add-column"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/add.png" width="16" height="16" /></a></th>
		</tr>
	<?php 
		foreach($files as $f) {
			$pf = new Permissions($f);
			if (!isset($striped) || $striped == 'ccm-list-record-alt') {
				$striped = '';
			} else if ($striped == '') { 
				$striped = 'ccm-list-record-alt';
			}
			$star_icon = ($f->isStarred() == 1) ? 'star_yellow.png' : 'star_grey.png';
			$fv = $f->getApprovedVersion(); 
			$canViewInline = $fv->canView() ? 1 : 0;
			$canEdit = ($fv->canEdit() && $pf->canWrite()) ? 1 : 0;
			?>
			<tr class="ccm-list-record <?php echo $striped?>" ccm-file-manager-instance="<?php echo $searchInstance?>" ccm-file-manager-can-admin="<?php echo ($pf->canAdmin())?>" ccm-file-manager-can-delete="<?php echo $pf->canAdmin()?>" ccm-file-manager-can-view="<?php echo $canViewInline?>" ccm-file-manager-can-replace="<?php echo $pf->canWrite()?>" ccm-file-manager-can-edit="<?php echo $canEdit?>" fID="<?php echo $f->getFileID()?>" id="fID<?php echo $f->getFileID()?>">
			<td class="ccm-file-list-cb" style="vertical-align: middle !important"><input type="checkbox" value="<?php echo $f->getFileID()?>" /></td>
			<td>
				<div class="ccm-file-list-thumbnail">
					<div class="ccm-file-list-thumbnail-image" fID="<?php echo $f->getFileID()?>"><table border="0" cellspacing="0" cellpadding="0" height="70" width="100%"><tr><td align="center" fID="<?php echo $f->getFileID()?>" style="padding: 0px"><?php echo $fv->getThumbnail(1)?></td></tr></table></div>
				</div>
		
			<?php  if ($fv->hasThumbnail(2)) { ?>
				<div class="ccm-file-list-thumbnail-hover" id="fID<?php echo $f->getFileID()?>hoverThumbnail"><div><?php echo $fv->getThumbnail(2)?></div></div>
			<?php  } ?>

				</td>
			<td><?php echo $fv->getType()?></td>
			<td class="ccm-file-list-starred"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/<?php echo $star_icon?>" height="16" width="16" border="0" class="ccm-star" /></td>			
			<td class="ccm-file-list-filename"><?php echo $txt->highlightSearch(wordwrap($fv->getTitle(), 15, "\n", true), $keywords)?></td>
			<td><?php echo date(DATE_APP_DASHBOARD_SEARCH_RESULTS_FILES, strtotime($f->getDateAdded()))?></td>
			<td><?php echo date(DATE_APP_DASHBOARD_SEARCH_RESULTS_FILES, strtotime($fv->getDateAdded()))?></td>
			<td><?php echo $fv->getSize()?></td>
			<?php  
			$slist = FileAttributeKey::getColumnHeaderList();
			foreach($slist as $ak) { ?>
				<td><?php 
				$vo = $fv->getAttributeValueObject($ak);
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
		
		<div class="ccm-results-list-none"><?php echo t('No files found.')?></div>
		
	
	<?php  } 
	$fileList->displayPaging($bu, false, $soargs); ?>
	
</div>