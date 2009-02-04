<?
	Loader::model('file_list');
	$pl = new FileList();
	$pl->setItemsPerPage(4);
	//$pl->debug();
	$files = $pl->getPage();
	$html = Loader::helper('html');
	$pl->displaySummary();
	$txt = Loader::helper('text');
	
	$pagination = $pl->getPagination();
	
	if (count($files) > 0) { ?>
	
		<table border="0" cellspacing="0" cellpadding="0" id="ccm-file-list">
		<tr>
			<th><input type="checkbox" /></td>
			<th><select>
			
			</select>
			</th>
			<th>Type</th>
			<th><a href="<?=$pl->getSortByURL('fvFilename')?>"><?=t('Filename')?></a></th>
			<th><a href="<?=$pl->getSortByURL('fvTitle')?>"><?=t('Title')?></a></th>
			<th><a href="<?=$pl->getSortByURL('fvDateAdded')?>"><?=t('Date Added')?></a></th>
			<th><a href="<?=$pl->getSortByURL('fvSize')?>"><?=t('Size')?></a></th>
		</tr>
		
	
	
	
	<?
		foreach($files as $f) {
			$fv = $f->getActiveVersion(); ?>
			
			<tr>
			<td><input type="checkbox" /></td>
			<td class="ccm-file-list-thumbnail"><?=$fv->getThumbnail(1)?></td>
			<td><?=$fv->getType()?></td>
			<td><?=wordwrap($fv->getFileName(), 25, "\n", true)?></td>
			<td><?=wordwrap($fv->getTitle(), 25, "\n", true)?></td>
			<td><?=date('M d, Y g:ia', strtotime($f->getDateAdded()))?></td>
			<td><?=$fv->getSize()?></td>
			
			
			
			
			<?
		}

	?>
	
	</table>
	
	

	<? } 
	$pl->displayPaging();