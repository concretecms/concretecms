<? defined('C5_EXECUTE') or die("Access Denied."); ?> 
<?

$s1 = FileSet::getMySets();
$form = Loader::helper('form');
$html = Loader::helper('html');

if (count($s1) > 0) { ?>

<div id="ccm-search-advanced-sets">
	<div>
	<table border="0" cellspacing="0" cellpadding="0" id="ccm-file-search-advanced-sets-header">
	<tr>
		<td width="100%"><h2><?=t('Sets')?></h2></td>
		<td>

		<div class="ccm-file-sets-search-wrapper-input">
			<?=$form->text('fsSearchName', $searchRequest['fsSearchName'], array('autocomplete' => 'off'))?>
		</div>
		
		</td>
	</tr>
	</table>
	</div>
	
		
	<div class="ccm-file-search-advanced-sets-results">
	<ul id="ccm-file-search-advanced-sets-list">
	<? foreach($s1 as $fs) { 
		$pfs = new Permissions($fs);
		
		?>
		<li class="ccm-<?=$searchInstance?>-search-advanced-sets-cb">
		<div class="ccm-file-search-advanced-set-controls">
			<a href="<?=View::url('/dashboard/files/sets', 'view_detail', $fs->getFileSetID())?>"><?=$html->image('icons/wrench.png')?></a>
			<? if ($pfs->canDeleteFileSet()) { ?>
				<a href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/delete_set?fsID=<?=$fs->getFileSetID()?>&searchInstance=<?=$searchInstance?>" dialog-append-buttons="true" class="ccm-file-set-delete-window" dialog-title="<?=t('Delete File Set')?>" dialog-width="320" dialog-height="110" dialog-modal="false"><?=$html->image('icons/delete_small.png')?></a>
			<? } ?>
		</div>
		<?=$form->checkbox('fsID[' . $fs->getFileSetID() . ']', $fs->getFileSetID(), (is_array($searchRequest['fsID']) && in_array($fs->getFileSetID(), $searchRequest['fsID'])))?> <?=$form->label('fsID[' . $fs->getFileSetID() . ']', $fs->getFileSetName())?></li>
	<? } ?>
	</ul>
	</div>

	<div style="padding-left: 6px; padding-top: 6px" class="ccm-note"><?=$form->checkbox('fsIDNone', '1', $searchRequest['fsIDNone'] == 1, array('instance' => $searchInstance))?> <?=$form->label('fsIDNone', t('Display files in no sets.'))?></div>
	
</div>

	<script type="text/javascript">
	$(function() {
		$('a.ccm-file-set-delete-window').dialog();
	});	
	</script>
<? } ?>