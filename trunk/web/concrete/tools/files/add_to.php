<?
defined('C5_EXECUTE') or die(_("Access Denied."));
$u = new User();
$form = Loader::helper('form');
$fp = FilePermissions::getGlobal();
if (!$fp->canAccessFileManager()) {
	die(_("Access Denied."));
}
Loader::model('file_set');

$s1 = FileSet::getMySets();

$files = array();
if (is_array($_REQUEST['fID'])) {
	foreach($_REQUEST['fID'] as $fID) {
		$files[] = File::getByID($fID);
	}
} else {
	$files[] = File::getByID($_REQUEST['fID']);
}

$sets = array();
// tri state checkbox
// state 0 - none of the selected files are in the set
// state 1 - SOME of the selected files are in the set
// state 2 - ALL files are in the set

foreach($s1 as $fs) {
	
	$foundInSets = 0;

	foreach($files as $f) {
		if ($f->inFileSet($fs)) {
			$foundInSets++;
		}
	}

	if ($foundInSets == 0) {
		$state = 0;
	} else if ($foundInSets == count($files)) {
		$state = 2;
	} else {
		$state = 1;
	}
	
	$fs->state = $state;
	$sets[] = $fs;
}

if ($_POST['task'] == 'add_to_sets') {
	
	foreach($_POST as $key => $value) {
	
		if (preg_match('/fsID:/', $key)) {
			$fsIDst = explode(':', $key);
			$fsID = $fsIDst[1];
			
			// so the affected file set is $fsID, the state of the thing is $value
			$fs = FileSet::getByID($fsID);
			
			switch($value) {
				case '0':
					foreach($files as $f) {
						$fs->removeFileFromSet($f);
					}
					break;
				case '1':
					// do nothing
					break;
				case '2':
					foreach($files as $f) {
						$fs->addFileToSet($f);
					}
					break;
			}		
			
		}
	}

	if ($_POST['fsNew']) {
		$type = ($_POST['fsNewShare'] == 1) ? FileSet::TYPE_PUBLIC : FileSet::TYPE_PRIVATE;
		$fs = FileSet::createAndGetSet($_POST['fsNewText'], $type);
		print_r($fs);
		foreach($files as $f) {
			$fs->addFileToSet($f);
		}
	}
	exit;
}

	function checkbox($field, $value, $state, $miscFields = array()) {

		$mf = '';
		if (is_array($miscFields)) {
			foreach($miscFields as $k => $v) {
				$mf .= $k . '="' . $v . '" ';
			}
		}

		$src = ASSETS_URL_IMAGES . '/checkbox_state_' . $state . '.png';
						
		$str = '<a href="javascript:void(0)" ccm-tri-state-startup="' . $state . '" ccm-tri-state-selected="' . $state . '" ><input type="hidden" value="' . $state . '" name="' . $field . ':' . $value . '" /> <img width="16" height="16" src="' . $src . '" ' . $mf . ' /></a>';
		return $str;
	}


?>


<script type="text/javascript">
$(function() {
	ccm_alSetupSetsForm();
});
</script>

<form method="post" id="ccm-file-add-to-set-form" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/add_to/">
<?=$form->hidden('task', 'add_to_sets')?>
<? foreach($files as $f) { ?>
	<?=$form->hidden('fID[]' , $f->getFileID())?>
<? } ?>
<h1><?=t('File Sets')?></h1>

<? $s1 = FileSet::getMySets(); ?>
<? foreach($sets as $s) { ?>

	<div class="ccm-file-set-add-cb">
		<?=checkbox('fsID', $s->getFileSetID(), $s->state)?> <?=$s->getFileSetName()?>
	</div>
<? } ?>

<br/>
<hr />

<h2><?=t('Add to New Set')?></h2>

<?=$form->checkbox('fsNew', 1)?> <?=$form->text('fsNewText', array('style' => 'width: 250px'))?> <?=$form->checkbox('fsNewShare', 1, true)?> <?=t('Make set public')?>

<br/><br/>
<?
$h = Loader::helper('concrete/interface');
$b1 = $h->button_js(t('Update'), 'ccm_alSubmitSetsForm()', 'left');
print $b1;
?>
</form>