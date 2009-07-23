<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); ?> 
<script type="text/javascript">
	var CCM_STAR_STATES = {
		'unstarred':'star_grey.png',
		'starred':'star_yellow.png'
	};
	var CCM_STAR_ACTION    = 'files/star.php';
</script>
<div id="ccm-file-list-wrapper"><a name="ccm-file-list-wrapper-anchor"></a>
<?php 
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
				<option value="download"><?php echo t('Download')?></option>
				<option value="sets"><?php echo t('Sets')?></option>
				<option value="properties"><?php echo t('Properties')?></option>
				<option value="rescan"><?php echo t('Rescan')?></option>
				<option value="delete"><?php echo t('Delete')?></option>
			</select>
			</th>
			<th>Type</th>

			<th class="ccm-file-list-starred">&nbsp;</th>			
			<th class="ccm-file-list-filename <?php echo $fileList->getSearchResultsClass('fvTitle')?>"><a href="<?php echo $fileList->getSortByURL('fvTitle', 'asc', $bu)?>"><?php echo t('Title')?></a></th>
			<th class="<?php echo $fileList->getSearchResultsClass('fDateAdded')?>"><a href="<?php echo $fileList->getSortByURL('fDateAdded', 'asc', $bu)?>"><?php echo t('Added')?></a></th>
			<th class="<?php echo $fileList->getSearchResultsClass('fvDateAdded')?>"><a href="<?php echo $fileList->getSortByURL('fvDateAdded', 'asc', $bu)?>"><?php echo t('Active Version')?></a></th>
			<th class="<?php echo $fileList->getSearchResultsClass('fvSize')?>"><a href="<?php echo $fileList->getSortByURL('fvSize', 'asc', $bu)?>"><?php echo t('Size')?></a></th>
		</tr>
		
	
	
	
	<?php 
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
			<tr class="ccm-file-list-record <?php echo $striped?>" ccm-file-manager-can-admin="<?php echo ($pf->canAdmin())?>" ccm-file-manager-can-delete="<?php echo $pf->canAdmin()?>" ccm-file-manager-can-view="<?php echo $canViewInline?>" ccm-file-manager-can-replace="<?php echo $pf->canWrite()?>" ccm-file-manager-can-edit="<?php echo $canEdit?>" fID="<?php echo $f->getFileID()?>" id="fID<?php echo $f->getFileID()?>">
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
			<td><?php echo date('M d, Y g:ia', strtotime($f->getDateAdded()))?></td>
			<td><?php echo date('M d, Y g:ia', strtotime($fv->getDateAdded()))?></td>
			<td><?php echo $fv->getSize()?></td>
						
			<?php 
		}

	?>
	
	</table>
	
	

	<?php  } else { ?>
		
		<div id="ccm-file-list-none"><?php echo t('No files found.')?></div>
		
	
	<?php  } 
	$fileList->displayPaging($bu); ?>
	
</div>