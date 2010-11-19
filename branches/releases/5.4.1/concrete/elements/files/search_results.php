<?php  defined('C5_EXECUTE') or die("Access Denied."); ?> 
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
				<option value="duplicate"><?php echo t('Copy')?></option>
				<option value="delete"><?php echo t('Delete')?></option>
			</select>
			</th>

			<th class="ccm-file-list-starred">&nbsp;</th>
			<?php  foreach($columns->getColumns() as $col) { ?>
				<?php  if ($col->isColumnSortable()) { ?>
					<th class="<?php echo $fileList->getSearchResultsClass($col->getColumnKey())?>"><a href="<?php echo $fileList->getSortByURL($col->getColumnKey(), $col->getColumnDefaultSortDirection(), $bu, $soargs)?>"><?php echo $col->getColumnName()?></a></th>
				<?php  } else { ?>
					<th><?php echo t('Type')?></th>
				<?php  } ?>
			<?php  } ?>
			<th class="ccm-search-add-column-header"><?php  if ($_REQUEST['fssID'] < 1) { ?><a href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/files/customize_search_columns?searchInstance=<?php echo $searchInstance?>" id="ccm-search-add-column"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/column_preferences.png" width="16" height="16" /></a><?php  } ?></th>
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
			$pfg = FilePermissions::getGlobal();
			?>
			<tr class="ccm-list-record <?php echo $striped?>" ccm-file-manager-instance="<?php echo $searchInstance?>" ccm-file-manager-can-admin="<?php echo ($pf->canAdmin())?>" ccm-file-manager-can-duplicate="<?php echo ($pfg->canAddFileType($f->getExtension()))?>" ccm-file-manager-can-delete="<?php echo $pf->canAdmin()?>" ccm-file-manager-can-view="<?php echo $canViewInline?>" ccm-file-manager-can-replace="<?php echo $pf->canWrite()?>" ccm-file-manager-can-edit="<?php echo $canEdit?>" fID="<?php echo $f->getFileID()?>" id="fID<?php echo $f->getFileID()?>">
			<td class="ccm-file-list-cb" style="vertical-align: middle !important"><input type="checkbox" value="<?php echo $f->getFileID()?>" /></td>
			<td>
				<div class="ccm-file-list-thumbnail">
					<div class="ccm-file-list-thumbnail-image" fID="<?php echo $f->getFileID()?>"><table border="0" cellspacing="0" cellpadding="0" height="70" width="100%"><tr><td align="center" fID="<?php echo $f->getFileID()?>" style="padding: 0px"><?php echo $fv->getThumbnail(1)?></td></tr></table></div>
				</div>
		
			<?php  if ($fv->hasThumbnail(2)) { ?>
				<div class="ccm-file-list-thumbnail-hover" id="fID<?php echo $f->getFileID()?>hoverThumbnail"><div><?php echo $fv->getThumbnail(2)?></div></div>
			<?php  } ?>

				</td>
			<td class="ccm-file-list-starred"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/<?php echo $star_icon?>" height="16" width="16" border="0" class="ccm-star" /></td>
			<?php  foreach($columns->getColumns() as $col) { ?>
				<?php  // special one for keywords ?>				
				<?php  if ($col->getColumnKey() == 'fvTitle') { ?>
					<td class="ccm-file-list-filename"><div style="word-wrap: break-word; width: 100px"><?php echo $txt->highlightSearch($fv->getTitle(), $keywords)?></div></td>		
				<?php  } else { ?>
					<td><?php echo $col->getColumnValue($f)?></td>
				<?php  } ?>
			<?php  } ?>
			
			<?php  /*
			
			*/ ?>
			
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