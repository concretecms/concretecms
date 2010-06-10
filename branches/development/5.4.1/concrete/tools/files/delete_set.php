<?
defined('C5_EXECUTE') or die(_("Access Denied."));
$u = new User();
$form = Loader::helper('form');
$vt = Loader::helper('validation/token');
$fp = FilePermissions::getGlobal();
if (!$fp->canAccessFileManager()) {
	die(_("Access Denied."));
}

$fs = FileSet::getByID($_REQUEST['fsID']);
if (!is_object($fs)) {
	die(t('Invalid file set.'));
}

$fsp = new Permissions($fs);
if ($fsp->canDeleteFileSet()) {
	
	if ($_POST['task'] == 'delete_file_set') {
		if ($vt->validate("delete_file_set")) {			
			print_r($_REQUEST);
			$fs->delete();
		}
		exit;
	}

} else {
	die(t('You do not have permissions to remove this file set.'));
}

?>

<h1><?=t('Delete File Set')?></h1>

	<?=t('Are you sure you want to delete the following file set?')?><br/><br/>
	
	<strong><?=$fs->getFileSetName()?></strong>
	
	<br/><br/>
	<div class="ccm-note"><?=t('(Note: files within the set will not be removed.)')?></div>
	<br/><br/>
	
	<form id="ccm-<?=$searchInstance?>-delete-file-set-form" method="post" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/delete_set" onsubmit="return ccm_alDeleteFileSet(this)">
	<?=$form->hidden('task', 'delete_file_set')?>
	<?=$vt->output('delete_file_set');?>
	<?=$form->hidden('fsID', $_REQUEST['fsID']); ?>	
	<?=$form->hidden('searchInstance', $_REQUEST['searchInstance']); ?>	
	<? $ih = Loader::helper('concrete/interface')?>
	<?=$ih->submit(t('Delete'))?>
	<?=$ih->button_js(t('Cancel'), 'jQuery.fn.dialog.closeTop()', 'left')?>	
	
	</form>
	
<script type="text/javascript">
ccm_alDeleteFileSet = function(form) {
	jQuery.fn.dialog.showLoader();
	$(form).ajaxSubmit(function(r) { 
		jQuery.fn.dialog.hideLoader(); 
		jQuery.fn.dialog.closeTop();
		$("#ccm-<?=$_REQUEST['searchInstance']?>-sets-search-wrapper").load('<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/search_sets_reload', {'searchInstance': '<?=$_REQUEST['searchInstance']?>'}, function() {
			ccm_alSetupFileSetSearch('<?=$_REQUEST['searchInstance']?>');
		});
	});
	return false;
}
</script>