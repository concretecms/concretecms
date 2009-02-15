<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?> 
<script type="text/javascript">
	var CCM_STAR_STATES = {
		'unstarred':'star_grey.png',
		'starred':'star_yellow.png'
	};
	var CCM_STAR_ACTION    = 'files/star.php';
</script>
<div id="ccm-file-list-wrapper">
<?
	$fileList->displaySummary();
	$txt = Loader::helper('text');
	$keywords = $_REQUEST['fKeywords'];
	$bu = REL_DIR_FILES_TOOLS_REQUIRED . '/files/search_results';
	
	if (count($files) > 0) { ?>	
		<table border="0" cellspacing="0" cellpadding="0" id="ccm-file-list">
		<tr>
			<th><input type="checkbox" /></td>
			<th><select>
			
			</select>
			</th>
			<th>Type</th>

			<th class="ccm-file-list-starred">&nbsp;</th>			
			<th class="ccm-file-list-filename <?=$fileList->getSearchResultsClass('fvTitle')?>"><a href="<?=$fileList->getSortByURL('fvTitle', 'asc', $bu)?>"><?=t('Title')?></a></th>
			<th class="<?=$fileList->getSearchResultsClass('fvDateAdded')?>"><a href="<?=$fileList->getSortByURL('fvDateAdded', 'asc', $bu)?>"><?=t('Date Added')?></a></th>
			<th class="<?=$fileList->getSearchResultsClass('fvSize')?>"><a href="<?=$fileList->getSortByURL('fvSize', 'asc', $bu)?>"><?=t('Size')?></a></th>
			<th class="<?=$fileList->getSearchResultsClass('fvAuthorName')?>"><a href="<?=$fileList->getSortByURL('fvAuthorName', 'asc', $bu)?>"><?=t('Uploaded By')?></a></th>
		</tr>
		
	
	
	
	<?
		foreach($files as $f) {
			if (!isset($striped) || $striped == 'ccm-file-list-alt') {
				$striped = '';
			} else if ($striped == '') { 
				$striped = 'ccm-file-list-alt';
			}
			$star_icon = ($f->isStarred() == 1) ? 'star_yellow.png' : 'star_grey.png';
			$fv = $f->getApprovedVersion(); ?>
			
			<tr class="ccm-file-list-record <?=$striped?>" fID="<?=$f->getFileID()?>" id="fID<?=$f->getFileID()?>">
			<td><input type="checkbox" /></td>
			<td><div class="ccm-file-list-thumbnail">
				<div class="ccm-file-list-thumbnail-image" fID="<?=$f->getFileID()?>"><?=$fv->getThumbnail(1)?></div>
			
			</div>
		
			<? if ($fv->hasThumbnail(2)) { ?>
				<div class="ccm-file-list-thumbnail-hover" id="fID<?=$f->getFileID()?>hoverThumbnail"><div><?=$fv->getThumbnail(2)?></div></div>
			<? } ?>

				</td>
			<td><?=$fv->getType()?></td>
			<td class="ccm-file-list-starred"><img src="<?=ASSETS_URL_IMAGES?>/icons/<?=$star_icon?>" height="16" width="16" border="0" class="ccm-star" /></td>			
			<td class="ccm-file-list-filename"><?=$txt->highlightSearch(wordwrap($fv->getTitle(), 25, "\n", true), $keywords)?></td>
			<td><?=date('M d, Y g:ia', strtotime($f->getDateAdded()))?></td>
			<td><?=$fv->getSize()?></td>
			<td><?=$txt->highlightSearch($fv->getAuthorName(), $keywords)?></td>
			
			
			
			
			<?
		}

	?>
	
	</table>
	
	

	<? } else { ?>
		
		<div id="ccm-file-list-none"><?=t('No files found.')?></div>
		
	
	<? } 
	$fileList->displayPaging($bu); ?>
	
</div>