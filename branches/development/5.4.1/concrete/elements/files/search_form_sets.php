<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?> 
<?

$s1 = FileSet::getMySets();
$form = Loader::helper('form');

if (count($s1) > 0) { ?>

<div id="ccm-search-advanced-sets">
	<div>
	<table border="0" cellspacing="0" cellpadding="0" id="ccm-file-search-advanced-sets-header">
	<tr>
		<td width="100%"><h2><?=t('Set')?></h2></td>
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
	<? foreach($s1 as $fs) { ?>
		<li class="ccm-<?=$searchInstance?>-search-advanced-sets-cb"><?=$form->checkbox('fsID[' . $fs->getFileSetID() . ']', $fs->getFileSetID(), (is_array($searchRequest['fsID']) && in_array($fs->getFileSetID(), $searchRequest['fsID'])))?> <?=$form->label('fsID[' . $fs->getFileSetID() . ']', $fs->getFileSetName())?></li>
	<? } ?>
	</ul>
	</div>
	
	<hr/>
	
	<div><?=$form->checkbox('fsIDNone', '1', $searchRequest['fsIDNone'] == 1, array('instance' => $searchInstance))?> <?=$form->label('fsIDNone', t('Display files in no sets.'))?></div>
</div>

<? } ?>