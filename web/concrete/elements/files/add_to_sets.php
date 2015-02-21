<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="ccm-ui">
<? $form = Loader::helper('form'); ?>
<?

function checkbox($field, $value, $state, $miscFields = array()) {

	$mf = '';
	if (is_array($miscFields)) {
		foreach($miscFields as $k => $v) {
			$mf .= $k . '="' . $v . '" ';
		}
	}

	$src = ASSETS_URL_IMAGES . '/checkbox_state_' . $state . '.png';

	$str = '<input type="hidden" value="' . $state . '" name="' . $field . ':' . $value . '" /><a href="javascript:void(0)" ccm-tri-state-startup="' . $state . '" ccm-tri-state-selected="' . $state . '" ><img width="16" height="16" src="' . $src . '" ' . $mf . ' /></a>';
	return $str;
}


$s1 = FileSet::getMySets();

$files = array();
$extensions = array();

if (is_array($_REQUEST['fID'])) {
	foreach($_REQUEST['fID'] as $fID) {
		$f = File::getByID($fID);
		$fp = new Permissions($f);
		if ($fp->canViewFile()) {
			$files[] = $f;
			$extensions[] = strtolower($f->getExtension());
		}
	}
} else {
	$f = File::getByID($_REQUEST['fID']);
	$fp = new Permissions($f);
	if ($fp->canViewFile()) {
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

	$r = new \Concrete\Core\File\EditResponse();
	$r->setFiles($files);
	$r->setMessage(t('File sets saved successfully.'));

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
		foreach ($files as $f) {
			$fs->addFileToSet($f);
		}
	}

	$r->outputJSON();

}
?>

<script type="text/javascript">
$(function() {
	$('#fsAddToSearchName').liveUpdate('ccm-file-search-add-to-sets-list', 'fileset').closest('form').unbind('submit.liveupdate');

	$("#ccm-add-to-set-form input[name=fsNew]").click(function() {
		if (!$(this).prop('checked')) {
			$("#ccm-add-to-set-form input[name=fsNewText]").val('');
		}
	});

	// Setup the tri-state checkboxes
	$('.ccm-file-set-add-cb a').each(function() {
		var cb = $(this);
		var startingState = cb.attr("ccm-tri-state-startup");
		$(this).click(function() {
			var selectedState = $(this).attr("ccm-tri-state-selected");
			var toSetState = 0;
			switch(selectedState) {
				case '0':
					if (startingState == '1') {
						toSetState = '1';
					} else {
						toSetState = '2';
					}
					break;
				case '1':
					toSetState = '2';
					break;
				case '2':
					toSetState = '0';
					break;
			}

			$(this).attr('ccm-tri-state-selected', toSetState);
			$(this).parent().find('input').val(toSetState);
			$(this).find('img').attr('src', CCM_IMAGE_PATH + '/checkbox_state_' + toSetState + '.png');
		});
	});


});

</script>

<? if (!$disableForm) { ?>
	<form method="post" class="form-stacked" data-dialog-form="file-sets" id="ccm-add-to-set-form" action="<?=Loader::helper('concrete/urls')->getToolsURL('files/add_to')?>">
	<?=$form->hidden('task', 'add_to_sets')?>
	<? foreach($files as $f) { ?>
		<input type="hidden" name="fID[]" value="<?=$f->getFileID();?>" />
	<? } ?>

<? } ?>

    <fieldset class="form-inline">
        <legend><?=t('Add to New Set')?></legend>

        <?=$form->checkbox('fsNew', 1)?> <?=$form->text('fsNewText', array('style' => 'width: 120px', 'onclick' => '$(\'input[name=fsNew]\').attr(\'checked\',true)'))?> <?=$form->checkbox('fsNewShare', 1, true)?> <?=t('Make set public')?>

    </fieldset>


	<br/>
	<fieldset>
		<legend><?=t('Existing Set')?></legend>

	<? $s1 = FileSet::getMySets(); ?>
	<? if (count($s1) > 0) { ?>
        <?php if(count($s1) > 10) { // show filter form if there are a bunch of sets ?>
            <div class="form-group">
                <form class="form-inline">
                    <?=$form->text('fsAddToSearchName', $searchRequest['fsSearchName'], array('autocomplete' => 'off', 'placeholder'=>t('Filter Sets')))?>
                </form>
            </div>
        <? } ?>

        <div class="form-group">
			<ul id="ccm-file-search-add-to-sets-list" class="list-unstyled">


		<? foreach($sets as $s) {
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
				<li class="ccm-file-set-add-cb">
				<label>
					<?=checkbox('fsID', $s->getFileSetID(), $s->state)?>
					<span><?=$s->getFileSetDisplayName()?></span>
				</label>
				</li>
		<? }
		} ?>

			</ul>
		</div>
		<? } else { ?>
			<?=t('You have not created any file sets yet.')?>
		<? } ?>

	</fieldset>
<? if (count($extensions) > 1) { ?>

	<div class="alert-message info"><p><?=t('If a file set does not appear above, you either have no access to add files to it, or it does not accept the file types %s.', implode(', ', $extensions));?></p></div>

<? } ?>


<? if (!$disableForm) { ?>

<div class="dialog-buttons">
	<input type="button" data-dialog-button="submit" value="<?=t('Update')?>" class="btn btn-primary pull-right" onclick="$('#ccm-add-to-set-form').submit()" />
</div>

	</form>

<? } ?>
</div>
