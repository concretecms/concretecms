<?
	Loader::model('file_list');
	$pl = new FileList();
	$files = $pl->getPage();
	$html = Loader::helper('html');
	
	if (count($files) > 0) { ?>
	
		<table border="0" cellspacing="0" cellpadding="0" id="ccm-file-list">
		<tr>
			<th><input type="checkbox" /></td>
			<th><select>
			
			</select>
			</th>
			<th>Type</th>
			<th>Filename</th>
			<th>Title</th>
			<th>Date</th>
			<th>Size</th>
		</tr>
		
	
	
	
	<?
		foreach($files as $f) {
			$fv = $f->getActiveVersion(); ?>
			
			<tr>
			<td style="text-align: center"><input type="checkbox" /></td>
			<td style="text-align: center"><?=$html->image($fv->getThumbnailSRC(1))?></td>
			<td><?=$fv->getType()?></td>
			<td><?=$fv->getFileName()?></td>
			<td><?=$fv->getTitle()?></td>
			<td><?=date('M d, Y g:ia', strtotime($f->getDateAdded()))?></td>
			<td><?=$fv->getSize()?></td>
			
			
			
			
			<?
		}

	?>
	
	</table>
	
	

	<? } 
	