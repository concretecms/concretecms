<?php  defined('C5_EXECUTE') or die("Access Denied."); ?> 
<?php 

$s1 = FileSet::getMySets();
$form = Loader::helper('form');
$html = Loader::helper('html');

if (count($s1) > 0) { ?>

<div id="ccm-search-advanced-sets">
	<div>
	<table border="0" cellspacing="0" cellpadding="0" id="ccm-file-search-advanced-sets-header">
	<tr>
		<td width="100%"><h2><?php echo t('Sets')?></h2></td>
		<td>

		<div class="ccm-file-sets-search-wrapper-input">
			<?php echo $form->text('fsSearchName', $searchRequest['fsSearchName'], array('autocomplete' => 'off'))?>
		</div>
		
		</td>
	</tr>
	</table>
	</div>
	
		
	<div class="ccm-file-search-advanced-sets-results">
	<ul id="ccm-file-search-advanced-sets-list">
	<?php  foreach($s1 as $fs) { 
		$pfs = new Permissions($fs);
		
		?>
		<li class="ccm-<?php echo $searchInstance?>-search-advanced-sets-cb">
		<div class="ccm-file-search-advanced-set-controls">
			<a href="<?php echo View::url('/dashboard/files/sets', 'view_detail', $fs->getFileSetID())?>"><?php echo $html->image('icons/wrench.png')?></a>
			<?php  if ($pfs->canDeleteFileSet()) { ?>
				<a href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/files/delete_set?fsID=<?php echo $fs->getFileSetID()?>&searchInstance=<?php echo $searchInstance?>" class="ccm-file-set-delete-window" dialog-title="<?php echo t('Delete File Set')?>" dialog-width="320" dialog-height="200" dialog-modal="false"><?php echo $html->image('icons/delete_small.png')?></a>
			<?php  } ?>
		</div>
		<?php echo $form->checkbox('fsID[' . $fs->getFileSetID() . ']', $fs->getFileSetID(), (is_array($searchRequest['fsID']) && in_array($fs->getFileSetID(), $searchRequest['fsID'])))?> <?php echo $form->label('fsID[' . $fs->getFileSetID() . ']', $fs->getFileSetName())?></li>
	<?php  } ?>
	</ul>
	</div>

	<div style="padding-left: 6px; padding-top: 6px" class="ccm-note"><?php echo $form->checkbox('fsIDNone', '1', $searchRequest['fsIDNone'] == 1, array('instance' => $searchInstance))?> <?php echo $form->label('fsIDNone', t('Display files in no sets.'))?></div>
	
</div>

	<script type="text/javascript">
	$(function() {
		$('a.ccm-file-set-delete-window').dialog();
	});	
	</script>
<?php  } ?>