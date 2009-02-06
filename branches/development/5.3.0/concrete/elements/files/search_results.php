<div id="ccm-file-list-wrapper">
<?
	$fileList->displaySummary();
	$txt = Loader::helper('text');
	$keywords = $_REQUEST['fKeywords'];

	if (count($files) > 0) { ?>
	
		<table border="0" cellspacing="0" cellpadding="0" id="ccm-file-list">
		<tr>
			<th><input type="checkbox" /></td>
			<th><select>
			
			</select>
			</th>
			<th>Type</th>
			<th><a href="<?=$fileList->getSortByURL('fvTitle')?>"><?=t('Title')?></a></th>
			<th><a href="<?=$fileList->getSortByURL('fvDateAdded')?>"><?=t('Date Added')?></a></th>
			<th><a href="<?=$fileList->getSortByURL('fvSize')?>"><?=t('Size')?></a></th>
			<th><a href="<?=$fileList->getSortByURL('fvAuthorName')?>"><?=t('Uploaded By')?></a></th>
		</tr>
		
	
	
	
	<?
		foreach($files as $f) {
			if (!isset($striped) || $striped == 'ccm-file-list-alt') {
				$striped = '';
			} else if ($striped == '') { 
				$striped = 'ccm-file-list-alt';
			}
			
			$fv = $f->getActiveVersion(); ?>
			
			<tr class="ccm-file-list-record <?=$striped?>" id="fID<?=$f->getFileID()?>">
			<td><input type="checkbox" /></td>
			<td class="ccm-file-list-thumbnail"><?=$fv->getThumbnail(1)?></td>
			<td><?=$fv->getType()?></td>
			<td><?=$txt->highlightSearch(wordwrap($fv->getTitle(), 25, "\n", true), $keywords)?></td>
			<td><?=date('M d, Y g:ia', strtotime($f->getDateAdded()))?></td>
			<td><?=$fv->getSize()?></td>
			<td><?=$txt->highlightSearch($fv->getAuthorName(), $keywords)?></td>
			
			
			
			
			<?
		}

	?>
	
	</table>
	
	

	<? } 
	$fileList->displayPaging(); ?>
	
</div>