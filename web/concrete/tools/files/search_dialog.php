<?
defined('C5_EXECUTE') or die("Access Denied.");

$cp = FilePermissions::getGlobal();
if (!$cp->canAccessFileManager()) {
	die(t("Unable to access the file manager."));
}
Loader::model('file_list');

if (isset($_REQUEST['searchInstance'])) {
	$searchInstance = $_REQUEST['searchInstance'];
} else {
	$searchInstance = $page . time();
}
$ocID = $_REQUEST['ocID'];

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
Loader::element('files/search_results', array('searchInstance' => $searchInstance, 'searchRequest' => $searchRequest, 'columns' => $columns, 'searchType' => 'DIALOG', 'files' => $files, 'fileList' => $fileList)); $searchForm = ob_get_contents();
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

<ul class="tabs">
<li class="active"><a href="javascript:void(0)" onclick="$('#ccm-<?=$searchInstance?>-pane-options ul.tabs li').removeClass('active'); $(this).parent().addClass('active'); $('#ccm-<?=$searchInstance?>-pane-options div.ccm-file-manager-add-form').hide(); $('#ccm-<?=$searchInstance?>-pane-options div.ccm-file-manager-search-form').show();"><?=t('Search Files')?></a></li>
<li><a href="javascript:void(0)" onclick="$('#ccm-<?=$searchInstance?>-pane-options ul.tabs li').removeClass('active');  $(this).parent().addClass('active'); $('#ccm-<?=$searchInstance?>-pane-options div.ccm-file-manager-search-form').hide(); $('#ccm-<?=$searchInstance?>-pane-options div.ccm-file-manager-add-form').show();"><?=t('Add Files')?></a></li>
</ul>

<div class="ccm-file-manager-search-form"><? Loader::element('files/search_form_advanced', array('searchInstance' => $searchInstance, 'searchRequest' => $searchRequest, 'searchType' => 'DIALOG')); ?></div>
<div class="ccm-file-manager-add-form" style="display: none">
<? Loader::element('files/upload_single', array('searchInstance' => $searchInstance, 'ocID' => $ocID)); ?>
</div>
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