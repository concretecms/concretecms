<?
defined('C5_EXECUTE') or die("Access Denied.");
$u = new User();
$form = Loader::helper('form');
$vt = Loader::helper('validation/token');
$fp = FilePermissions::getGlobal();
if (!$fp->canAccessFileManager()) {
	die(t("Unable to access the file manager."));
}

$fs = FileSet::getByID($_REQUEST['fsID']);
if (!is_object($fs)) {
	die(t('Invalid file set.'));
}
$searchInstance = Loader::helper('text')->entities($_REQUEST['searchInstance']);

$fsp = new Permissions($fs);
if ($fsp->canDeleteFileSet()) {
	
	if ($_POST['task'] == 'delete_file_set') {
		if ($vt->validate("delete_file_set")) {			
			$fs->delete();
		}
		exit;
	}

} else {
	die(t('You do not have permissions to remove this file set.'));
}

?>

<div class="ccm-ui">

<p><?=t('Are you sure you want to delete the following file set?')?></p>
<p><strong><?=$fs->getFileSetName()?></strong></p>
<div class="help-block"><?=t('(Note: files within the set will not be removed.)')?></div>

	<form id="ccm-<?=$searchInstance?>-delete-file-set-form" method="post" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/delete_set" onsubmit="return ccm_alDeleteFileSet(this)">
	<?=$form->hidden('task', 'delete_file_set')?>
	<?=$vt->output('delete_file_set');?>
	<?=$form->hidden('fsID', $_REQUEST['fsID']); ?>	
	<?=$form->hidden('searchInstance', $searchInstance); ?>	
	<? $ih = Loader::helper('concrete/interface')?>

<div class="dialog-buttons">
	<?=$ih->button_js(t('Delete'), "ccm_alDeleteFileSet($('#ccm-" . $searchInstance . "-delete-file-set-form').get(0))", 'right', 'error')?>
	<?=$ih->button_js(t('Cancel'), 'jQuery.fn.dialog.closeTop()', 'left')?>	
</div>

	</form>
</div>

<script type="text/javascript">
ccm_alDeleteFileSet = function(form) {
	jQuery.fn.dialog.showLoader();
	$(form).ajaxSubmit(function(r) { 
		jQuery.fn.dialog.hideLoader(); 
		jQuery.fn.dialog.closeTop();
		
		<? if ($fs->getFileSetType() == FileSet::TYPE_SAVED_SEARCH) { ?>
			if (ccm_alLaunchType['<?=$searchInstance?>'] == 'DASHBOARD') {
				window.location.href = "<?=View::url('/dashboard/files/search')?>";
			} else {
				var url = $("div#ccm-<?=$searchInstance?>-overlay-wrapper input[name=dialogAction]").val() + "&refreshDialog=1";
				$.get(url, function(resp) {
					jQuery.fn.dialog.hideLoader();
					$("div#ccm-<?=$searchInstance?>-overlay-wrapper").html(resp);
				});
			}
		<? } else { ?>
			$("#ccm-<?=$searchInstance?>-sets-search-wrapper").load('<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/search_sets_reload', {'searchInstance': '<?=$searchInstance?>'}, function() {
				ccm_alSetupFileSetSearch('<?=$searchInstance?>');
			});
		<? } ?>
	});
	return false;
}
</script>