<? defined('C5_EXECUTE') or die("Access Denied."); ?> 
<?
if (isset($_REQUEST['searchInstance'])) {
	$searchInstance = Loader::helper('text')->entities($_REQUEST['searchInstance']);
}
?>
<script type="text/javascript">
	var CCM_STAR_STATES = {
		'unstarred':'star_grey.png',
		'starred':'star_yellow.png'
	};
	var CCM_STAR_ACTION    = 'files/star.php';
</script>

<div id="ccm-<?=$searchInstance?>-search-results" class="ccm-file-list">

<? if ($searchType == 'DASHBOARD') { ?>

<div class="ccm-pane-body">

<? } ?>

<div id="ccm-list-wrapper"><a name="ccm-<?=$searchInstance?>-list-wrapper-anchor"></a>
		<? $form = Loader::helper('form'); ?>
	<div style="float: left; width: 200px; margin-bottom: 10px">
		<select id="ccm-<?=$searchInstance?>-list-multiple-operations" class="" disabled>
			<option value="">** <?=t('With Selected')?></option>
			<option value="download"><?=t('Download')?></option>
			<option value="sets"><?=t('Sets')?></option>
			<option value="properties"><?=t('Properties')?></option>
			<option value="rescan"><?=t('Rescan')?></option>
			<option value="duplicate"><?=t('Copy')?></option>
			<option value="delete"><?=t('Delete')?></option>
		</select>	
		
	</div>
	<div style="float: right">
		<? Loader::element('files/upload_single', array('searchInstance' => $searchInstance, 'ocID' => $ocID)); ?>
	</div>
	<div class="clearfix" style="height: 1px"></div>

<?
	$txt = Loader::helper('text');
	$keywords = $searchRequest['fKeywords'];
	$soargs = array();
	$soargs['searchType'] = $searchType;
	$soargs['searchInstance'] = $searchInstance;
	$bu = REL_DIR_FILES_TOOLS_REQUIRED . '/files/search_results';
	
	if (count($files) > 0) { ?>	
		<table border="0" cellspacing="0" cellpadding="0" id="ccm-<?=$searchInstance?>-list" class="ccm-results-list">
		<tr>
			<th><input id="ccm-<?=$searchInstance?>-list-cb-all" type="checkbox" /></th>
			<th class="ccm-file-list-thumbnail-wrapper"><?=t('Thumbnail')?></th>

			<th class="ccm-file-list-starred">&nbsp;</th>
			<? foreach($columns->getColumns() as $col) { ?>
				<? if ($col->isColumnSortable()) { ?>
					<th class="<?=$fileList->getSearchResultsClass($col->getColumnKey())?>"><a href="<?=$fileList->getSortByURL($col->getColumnKey(), $col->getColumnDefaultSortDirection(), $bu, $soargs)?>"><?=$col->getColumnName()?></a></th>
				<? } else { ?>
					<th><?=$col->getColumnName()?></th>
				<? } ?>
			<? } ?>
		</tr>
	<?
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
			$canEdit = ($fv->canEdit() && $pf->canEditFileContents()) ? 1 : 0;
			$pfg = FilePermissions::getGlobal();
			?>
			<tr class="ccm-list-record <?=$striped?>" ccm-file-manager-instance="<?=$searchInstance?>" ccm-file-manager-can-admin="<?=($pf->canEditFilePermissions())?>" ccm-file-manager-can-duplicate="<?=$pf->canCopyFile()?>" ccm-file-manager-can-delete="<?=$pf->canDeleteFile()?>" ccm-file-manager-can-view="<?=$canViewInline?>" ccm-file-manager-can-replace="<?=$pf->canEditFileContents()?>" ccm-file-manager-can-edit="<?=$canEdit?>" fID="<?=$f->getFileID()?>" id="fID<?=$f->getFileID()?>">
			<td class="ccm-file-list-cb" style="vertical-align: middle !important"><input type="checkbox" value="<?=$f->getFileID()?>" /></td>
			<td class="ccm-file-list-thumbnail-wrapper"><ul class="thumbnails"><li class="ccm-file-list-thumbnail" fID="<?=$f->getFileID()?>"><a href="javascript:void(0)" class="thumbnail"><?=$fv->getThumbnail(1)?></a></li></ul>
			
			<? if ($fv->hasThumbnail(2)) { ?>
				<div class="ccm-file-list-thumbnail-hover" id="fID<?=$f->getFileID()?>hoverThumbnail"><div><?=$fv->getThumbnail(2)?></div></div>
			<? } ?>

				</td>
			<td class="ccm-file-list-starred"><img src="<?=ASSETS_URL_IMAGES?>/icons/<?=$star_icon?>" height="16" width="16" border="0" class="ccm-star" /></td>
			<? foreach($columns->getColumns() as $col) { ?>
				<? // special one for keywords ?>				
				<? if ($col->getColumnKey() == 'fvTitle') { ?>
					<td class="ccm-file-list-filename"><?=$txt->highlightSearch($fv->getTitle(), $keywords)?></td>		
				<? } else { ?>
					<td><?=$col->getColumnValue($f)?></td>
				<? } ?>
			<? } ?>
						
			</tr>
			<?
		}

	?>
	
	</table>
	
	

	<? } else { ?>
		
		<div class="ccm-results-list-none"><?=t('No files found.')?></div>
		
	
	<? } ?>
	
<?
	$fileList->displaySummary();
?>
</div>

<? if ($searchType == 'DASHBOARD') { ?>
</div>
<div class="ccm-pane-footer">
	<? 	$fileList->displayPagingV2($bu, false, $soargs); ?>
</div>

<? } else { ?>
	<div class="ccm-pane-dialog-pagination">
		<? 	$fileList->displayPagingV2($bu, false, $soargs); ?>
	</div>
<? } ?>

</div>
