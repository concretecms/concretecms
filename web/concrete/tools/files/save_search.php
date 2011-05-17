<?
defined('C5_EXECUTE') or die("Access Denied.");
$u = new User();
$form = Loader::helper('form');
$vt = Loader::helper('validation/token');
$fp = FilePermissions::getGlobal();
if (!$fp->canAccessFileManager()) {
	die(t("Access Denied."));
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
	$fsa = FileSetSavedSearch::add(Loader::helper('text')->entities($_POST['fsName']), $req, $colset);
	print $fsa->getFileSetID();
	exit;
}

?>

<h1><?=t('Save Search')?></h1>

<form id="ccm-<?=$searchInstance?>-save-search-form" method="post" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/save_search" onsubmit="return ccm_alSaveSearch(this)">
<?=$form->hidden('task', 'save_search')?>
<?=$form->hidden('searchInstance', $_REQUEST['searchInstance']); ?>	
<? $ih = Loader::helper('concrete/interface')?>
<p><?=t('Enter a name for this saved search file set.')?></p>
<?=$form->text('fsName', array('style' => 'width: 200px'))?>

<?=$ih->submit(t('Save Search'))?>
<br/><br/>

<?=$ih->button_js(t('Cancel'), 'jQuery.fn.dialog.closeTop()', 'left')?>	

</form>
	
<script type="text/javascript">
ccm_alSaveSearch = function(form) {
	if ($("input[name=fsName]").val() == '') {
		alert('<?=t("You must enter a valid name")?>');
	} else {
		jQuery.fn.dialog.showLoader();
		$(form).ajaxSubmit(function(r) { 
			jQuery.fn.dialog.hideLoader(); 
			jQuery.fn.dialog.closeTop();
			if (ccm_alLaunchType['<?=$_REQUEST['searchInstance']?>'] == 'DASHBOARD') {
				window.location.href = "<?=View::url('/dashboard/files/search')?>?fssID=" + r;			
			} else {
				var url = $("div#ccm-<?=$_REQUEST['searchInstance']?>-overlay-wrapper input[name=dialogAction]").val() + "&refreshDialog=1&fssID=" + r;
				$.get(url, function(resp) {
					jQuery.fn.dialog.hideLoader();
					$("div#ccm-<?=$_REQUEST['searchInstance']?>-overlay-wrapper").html(resp);
				});		
			}
		});
	}
	return false;
}
</script>