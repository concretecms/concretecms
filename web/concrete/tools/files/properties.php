<?
/*

if ($_POST['task'] == 'approve_version' && $fp->canEditFileProperties() && (!$previewMode)) {
	$fv->approve();
	exit;
}

if ($_POST['task'] == 'delete_version' && $fp->canEditFileContents() && (!$previewMode)) {
	$fv->delete();
	exit;
}

</div>
</div>

<? if (!$previewMode) { ?>
	
	<div class="ccm-tab-content" id="ccm-tab-content-versions">
	
		<h3><?=t('File Versions')?></h3>
	
		<table border="0" cellspacing="0" width="100%" id="ccm-file-versions-grid" class="ccm-grid" cellpadding="0">
		<tr>
			<th>&nbsp;</th>
			<th><?=t('Filename')?></th>
			<th><?=t('Title')?></th>
			<th><?=t('Comments')?></th>
			<th><?=t('Creator')?></th>
			<th><?=t('Added On')?></th>
			<? if ($fp->canEditFileContents()) { ?>
				<th>&nbsp;</th>
			<? } ?>
		</tr>
		<?
		$versions = $f->getVersionList();
		foreach($versions as $fvv) { ?>
			<tr fID="<?=$f->getFileID()?>" fvID="<?=$fvv->getFileVersionID()?>" <? if ($fvv->getFileVersionID() == $fv->getFileVersionID()) { ?> class="ccm-file-versions-grid-active" <? } ?>>
				<td style="text-align: center">
					<?=$form->radio('vlfvID', $fvv->getFileVersionID(), $fvv->getFileVersionID() == $fv->getFileVersionID())?>
				</td>
				<td width="100">
					<div style="width: 150px; word-wrap: break-word">
					<a href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/properties?fID=<?=$f->getFileID()?>&fvID=<?=$fvv->getFileVersionID()?>&task=preview_version" dialog-modal="false" dialog-width="630" dialog-height="450" dialog-title="<?=t('Preview File')?>" class="dialog-launch">
						<?=$fvv->getFilename()?>
					</a>
					</div>
				</td>
				<td> 
					<div style="width: 150px; word-wrap: break-word">
						<?=$fvv->getTitle()?>
					</div>
				</td>
				<td><?
					$comments = $fvv->getVersionLogComments();
					if (count($comments) > 0) {
						print t('Updated ');
	
						for ($i = 0; $i < count($comments); $i++) {
							print $comments[$i];
							if (count($comments) > ($i + 1)) {
								print ', ';
							}
						}
						
						print '.';
					}
					?>
					</td>
				<td><?=$fvv->getAuthorName()?></td>
				<td><?=$dateHelper->date(DATE_APP_FILE_VERSIONS, strtotime($fvv->getDateAdded()))?></td>
				<? if ($fp->canEditFileContents()) { ?>
					<? if ($fvv->getFileVersionID() == $fv->getFileVersionID()) { ?>
						<td>&nbsp;</td>
					<? } else { ?>
						<td><a class="ccm-file-versions-remove" href="javascript:void(0)"><?=t('Delete')?></a></td>
					<? } ?>
				<? } ?>
			</tr>	
		
		<? } ?>
		
		</table>
	
	</div>

<? } ?>


</div>