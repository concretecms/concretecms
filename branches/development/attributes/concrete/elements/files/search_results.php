<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?> 
<script type="text/javascript">
	var CCM_STAR_STATES = {
		'unstarred':'star_grey.png',
		'starred':'star_yellow.png'
	};
	var CCM_STAR_ACTION    = 'files/star.php';
</script>
<div id="ccm-file-list-wrapper"><a name="ccm-file-list-wrapper-anchor"></a>
<?
	$fileList->displaySummary();
	$txt = Loader::helper('text');
	$keywords = $_REQUEST['fKeywords'];
	$bu = REL_DIR_FILES_TOOLS_REQUIRED . '/files/search_results';
	
	if (count($files) > 0) { ?>	
		<table border="0" cellspacing="0" cellpadding="0" id="ccm-file-list">
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

			<th class="ccm-file-list-starred">&nbsp;</th>			
			<th class="ccm-file-list-filename <?=$fileList->getSearchResultsClass('fvTitle')?>"><a href="<?=$fileList->getSortByURL('fvTitle', 'asc', $bu)?>"><?=t('Title')?></a></th>
			<th class="<?=$fileList->getSearchResultsClass('fDateAdded')?>"><a href="<?=$fileList->getSortByURL('fDateAdded', 'asc', $bu)?>"><?=t('Added')?></a></th>
			<th class="<?=$fileList->getSearchResultsClass('fvDateAdded')?>"><a href="<?=$fileList->getSortByURL('fvDateAdded', 'asc', $bu)?>"><?=t('Active Version')?></a></th>
			<th class="<?=$fileList->getSearchResultsClass('fvSize')?>"><a href="<?=$fileList->getSortByURL('fvSize', 'asc', $bu)?>"><?=t('Size')?></a> <a href="javascript:void(0)" id="ccm-file-search-add-option"><img src="<?=ASSETS_URL_IMAGES?>/icons/add.png" width="16" height="16" style="float: right"/></a></th>
		</tr>
		
	
	
	
	<?
		foreach($files as $f) {
			$pf = new Permissions($f);
			if (!isset($striped) || $striped == 'ccm-file-list-alt') {
				$striped = '';
			} else if ($striped == '') { 
				$striped = 'ccm-file-list-alt';
			}
			$star_icon = ($f->isStarred() == 1) ? 'star_yellow.png' : 'star_grey.png';
			$fv = $f->getApprovedVersion(); 
			$canViewInline = $fv->canView() ? 1 : 0;
			$canEdit = ($fv->canEdit() && $pf->canWrite()) ? 1 : 0;
			?>
			<tr class="ccm-file-list-record <?=$striped?>" ccm-file-manager-can-admin="<?=($pf->canAdmin())?>" ccm-file-manager-can-delete="<?=$pf->canAdmin()?>" ccm-file-manager-can-view="<?=$canViewInline?>" ccm-file-manager-can-replace="<?=$pf->canWrite()?>" ccm-file-manager-can-edit="<?=$canEdit?>" fID="<?=$f->getFileID()?>" id="fID<?=$f->getFileID()?>">
			<td class="ccm-file-list-cb" style="vertical-align: middle !important"><input type="checkbox" value="<?=$f->getFileID()?>" /></td>
			<td>
				<div class="ccm-file-list-thumbnail">
					<div class="ccm-file-list-thumbnail-image" fID="<?=$f->getFileID()?>"><table border="0" cellspacing="0" cellpadding="0" height="70" width="100%"><tr><td align="center" fID="<?=$f->getFileID()?>" style="padding: 0px"><?=$fv->getThumbnail(1)?></td></tr></table></div>
				</div>
		
			<? if ($fv->hasThumbnail(2)) { ?>
				<div class="ccm-file-list-thumbnail-hover" id="fID<?=$f->getFileID()?>hoverThumbnail"><div><?=$fv->getThumbnail(2)?></div></div>
			<? } ?>

				</td>
			<td><?=$fv->getType()?></td>
			<td class="ccm-file-list-starred"><img src="<?=ASSETS_URL_IMAGES?>/icons/<?=$star_icon?>" height="16" width="16" border="0" class="ccm-star" /></td>			
			<td class="ccm-file-list-filename"><?=$txt->highlightSearch(wordwrap($fv->getTitle(), 15, "\n", true), $keywords)?></td>
			<td><?=date('M d, Y g:ia', strtotime($f->getDateAdded()))?></td>
			<td><?=date('M d, Y g:ia', strtotime($fv->getDateAdded()))?></td>
			<td><?=$fv->getSize()?></td>
						
			<?
		}

	?>
	
	</table>
	
	

	<? } else { ?>
		
		<div id="ccm-file-list-none"><?=t('No files found.')?></div>
		
	
	<? } 
	$fileList->displayPaging($bu); ?>
	
</div>