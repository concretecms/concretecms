<?php 
defined('C5_EXECUTE') or die("Access Denied.");
$u = new User();
$form = Loader::helper('form');
$vt = Loader::helper('validation/token');
$fp = FilePermissions::getGlobal();
if (!$fp->canAccessFileManager()) {
	die(_("Access Denied."));
}

if ($_POST['task'] == 'save_search') {
	Loader::model('file_set');
	Loader::model('file_list');
	$cnt = Loader::controller('/dashboard/files/search');
	$fileList = $cnt->getRequestedSearchResults();
	$req = $fileList->getSearchRequest();
	$colset = FileManagerColumnSet::getCurrent();
	
	if ($req['ccm_order_by'] != '' && $req['ccm_order_dir'] != '') {
		$colset->setDefaultSortColumn($colset->getColumnByKey($req['ccm_order_by']), $req['ccm_order_dir']);
	}
	$fsa = FileSetSavedSearch::add($_POST['fsName'], $req, $colset);
	print $fsa->getFileSetID();
	exit;
}

?>

<h1><?php echo t('Save Search')?></h1>

<form id="ccm-<?php echo $searchInstance?>-save-search-form" method="post" action="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/files/save_search" onsubmit="return ccm_alSaveSearch(this)">
<?php echo $form->hidden('task', 'save_search')?>
<?php echo $form->hidden('searchInstance', $_REQUEST['searchInstance']); ?>	
<?php  $ih = Loader::helper('concrete/interface')?>
<p><?php echo t('Enter a name for this saved search file set.')?></p>
<?php echo $form->text('fsName', array('style' => 'width: 200px'))?>

<?php echo $ih->submit(t('Save Search'))?>
<br/><br/>

<?php echo $ih->button_js(t('Cancel'), 'jQuery.fn.dialog.closeTop()', 'left')?>	

</form>
	
<script type="text/javascript">
ccm_alSaveSearch = function(form) {
	if ($("input[name=fsName]").val() == '') {
		alert('<?php echo t("You must enter a valid name")?>');
	} else {
		jQuery.fn.dialog.showLoader();
		$(form).ajaxSubmit(function(r) { 
			jQuery.fn.dialog.hideLoader(); 
			jQuery.fn.dialog.closeTop();
			if (ccm_alLaunchType['<?php echo $_REQUEST['searchInstance']?>'] == 'DASHBOARD') {
				window.location.href = "<?php echo View::url('/dashboard/files/search')?>?fssID=" + r;			
			} else {
				var url = $("div#ccm-<?php echo $_REQUEST['searchInstance']?>-overlay-wrapper input[name=dialogAction]").val() + "&refreshDialog=1&fssID=" + r;
				$.get(url, function(resp) {
					jQuery.fn.dialog.hideLoader();
					$("div#ccm-<?php echo $_REQUEST['searchInstance']?>-overlay-wrapper").html(resp);
				});		
			}
		});
	}
	return false;
}
</script>