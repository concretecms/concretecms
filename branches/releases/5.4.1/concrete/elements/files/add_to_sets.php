<?php  defined('C5_EXECUTE') or die("Access Denied."); ?> 
<?php  $form = Loader::helper('form'); ?>
<?php 

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

Loader::model('file_set');

$s1 = FileSet::getMySets();

$files = array();
$searchInstance = $_REQUEST['searchInstance'];
$extensions = array();

if (is_array($_REQUEST['fID'])) {
	foreach($_REQUEST['fID'] as $fID) {
		$f = File::getByID($fID);
		$fp = new Permissions($f);
		if ($fp->canRead()) {
			$files[] = $f;
			$extensions[] = strtolower($f->getExtension());
		}
	}
} else {
	$f = File::getByID($_REQUEST['fID']);
	$fp = new Permissions($f);
	if ($fp->canRead()) {
		$files[] = $f;
		$extensions[] = strtolower($f->getExtension());
	}
}

$extensions = array_unique($extensions);
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
			$fsp = new Permissions($fs);
			if ($fsp->canAddFile($f)) {
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
	}

	if ($_POST['fsNew']) {
		$type = ($_POST['fsNewShare'] == 1) ? FileSet::TYPE_PUBLIC : FileSet::TYPE_PRIVATE;
		$fs = FileSet::createAndGetSet($_POST['fsNewText'], $type);
		//print_r($fs);
		foreach($files as $f) {
			$fs->addFileToSet($f);
		}
	}
	exit;
}
?>

<script type="text/javascript">
$(function() {
	ccm_alSetupSetsForm('<?php echo $searchInstance?>');
});
</script>


<?php  if (!$disableForm) { ?>
	<form method="post" id="ccm-<?php echo $searchInstance?>-add-to-set-form" action="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/files/add_to/">
	<?php echo $form->hidden('task', 'add_to_sets')?>
	<?php  foreach($files as $f) { ?>
		<input type="hidden" name="fID[]" value="<?php echo $f->getFileID();?>" />
	<?php  } ?>

<?php  } ?>

	<div style="margin-top: 12px">
	<table border="0" cellspacing="0" cellpadding="0" id="ccm-file-search-advanced-sets-header">
	<tr>
		<td width="100%"><h1><?php echo t('Set')?></h1></td>
		<td>

		<div class="ccm-file-sets-search-wrapper-input">
			<?php echo $form->text('fsAddToSearchName', $searchRequest['fsSearchName'], array('autocomplete' => 'off'))?>
		</div>
		
		</td>
	</tr>
	</table>
	</div>


	<?php  $s1 = FileSet::getMySets(); ?>
	<?php  if (count($s1) > 0) { ?>
	<div class="ccm-file-search-advanced-sets-results">
		<ul id="ccm-file-search-add-to-sets-list">
	
	
	<?php  foreach($sets as $s) { 
		$displaySet = true;
		
		$pf = new Permissions($s);
		if (!$pf->canAddFiles()) { 
			$displaySet = false;
		} else {
			foreach($extensions as $ext) {
				if (!$pf->canAddFileType($ext)) {
					$displaySet = false;
				}
			}
		}
		
		if ($displaySet) {
		?>
	
		<li class="ccm-file-set-add-cb" style="padding-left: 0px">
			<?php echo checkbox('fsID', $s->getFileSetID(), $s->state)?> <label><?php echo $s->getFileSetName()?></label>
		</li>
	<?php  } 
	} ?>
	
		</ul>
	</div>
	<?php  } else { ?>
		<?php echo t('You have not created any file sets yet.')?>
	<?php  } ?>

<?php  if (count($extensions) > 1) { ?>

	<br/><div class="ccm-note"><?php echo t('If a file set does not appear above, you either have no access to add files to it, or it does not accept the file types %s.', implode(', ', $extensions));?></div>
	
	
<?php  } ?>
<br/>
<hr />

<h2><?php echo t('Add to New Set')?></h2>

<?php echo $form->checkbox('fsNew', 1)?> <?php echo $form->text('fsNewText', array('style' => 'width: 120px', 'onclick' => '$(\'input[name=fsNew]\').attr(\'checked\',true)'))?> <?php echo $form->checkbox('fsNewShare', 1, true)?> <?php echo t('Make set public')?>

<?php  if (!$disableForm) { ?>

	<br/><br/>
	<?php 
	$h = Loader::helper('concrete/interface');
	$b1 = $h->submit(t('Update'), false, 'left');
	print $b1;
	?>
	</form>
	
<?php  } ?>