<?
defined('C5_EXECUTE') or die("Access Denied.");

$cp = FilePermissions::getGlobal();
if ((!$cp->canAddFile()) && (!$cp->canSearchFiles())) {
	die(t("Unable to access the file manager."));
}
Loader::model('file_list');

if (isset($_REQUEST['searchInstance'])) {
	$searchInstance = Loader::helper('text')->entities($_REQUEST['searchInstance']);
} else {
	$searchInstance = $page . time();
}
$ocID = Loader::helper('text')->entities($_REQUEST['ocID']);

$cnt = Loader::controller('/dashboard/files/search');
$fileList = $cnt->getRequestedSearchResults();
$files = $fileList->getPage();
$pagination = $fileList->getPagination();
$searchRequest = $cnt->get('searchRequest');
$columns = $cnt->get('columns');

$alType = 'false';
if (isset($_REQUEST['disable_choose']) && $_REQUEST['disable_choose'] == 1) { 
	$alType = 'BROWSE';
}

ob_start();
Loader::element('files/search_results', array('ocID' => $ocID, 'searchInstance' => $searchInstance, 'searchRequest' => $searchRequest, 'columns' => $columns, 'searchType' => 'DIALOG', 'files' => $files, 'fileList' => $fileList)); $searchForm = ob_get_contents();
ob_end_clean();

$v = View::getInstance();
$v->outputHeaderItems();


?>

<? if (!isset($_REQUEST['refreshDialog'])) { ?> 
	<div id="ccm-<?=$searchInstance?>-overlay-wrapper">
<? } ?>
<div id="ccm-<?=$searchInstance?>-search-overlay" class="ccm-ui">
	<input type="hidden" name="dialogAction" value="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/search_dialog?ocID=<?=$_REQUEST['ocID']?>&searchInstance=<?=$searchInstance?>&disable_choose=<?=$_REQUEST['disable_choose']?>" />

<div class="ccm-pane-options" id="ccm-<?=$searchInstance?>-pane-options">

<div class="ccm-file-manager-search-form"><? Loader::element('files/search_form_advanced', array('searchInstance' => $searchInstance, 'searchRequest' => $searchRequest, 'searchType' => 'DIALOG')); ?></div>
</div>

<?=$searchForm?>

</div>

<? if (!isset($_REQUEST['refreshDialog'])) { ?> 
	</div>
<? } ?>
<?
print '<script type="text/javascript">
$(function() {
	ccm_activateFileManager(\'' . $alType . '\', \'' . $searchInstance . '\');
});
</script>';
?>