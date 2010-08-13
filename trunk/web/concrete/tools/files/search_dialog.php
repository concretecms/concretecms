<?
defined('C5_EXECUTE') or die(_("Access Denied."));

$cp = FilePermissions::getGlobal();
if (!$cp->canAccessFileManager()) {
	die(_("Unable to access the file manager."));
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

?>

<? if (!isset($_REQUEST['refreshDialog'])) { ?> 
	<div id="ccm-<?=$searchInstance?>-overlay-wrapper">
<? } ?>
<div id="ccm-<?=$searchInstance?>-search-overlay">
	<input type="hidden" name="dialogAction" value="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/search_dialog?ocID=<?=$_REQUEST['ocID']?>&searchInstance=<?=$searchInstance?>" />
		
		<table id="ccm-search-form-table" >
			<tr>
				<td valign="top" class="ccm-search-form-advanced-col">
					<? Loader::element('files/search_form_advanced', array('searchInstance' => $searchInstance, 'columns' => $columns, 'searchRequest' => $searchRequest)); ?>
				</td>		
				<? /* <div id="ccm-<?=$searchInstance?>-search-advanced-fields-gutter">&nbsp;</div> */ ?>		
				<td valign="top" width="100%">	
					
					<div id="ccm-search-advanced-results-wrapper">
					
						<? Loader::element('files/upload_single', array('searchInstance' => $searchInstance, 'ocID' => $ocID)); ?>
						
						<div id="ccm-<?=$searchInstance?>-search-results" class="ccm-file-list">
						
							<? Loader::element('files/search_results', array('searchInstance' => $searchInstance, 'columns' => $columns, 'searchRequest' => $searchRequest, 'files' => $files, 'fileList' => $fileList, 'pagination' => $pagination)); ?>
						
						</div>
					
					</div>
				
				</td>	
			</tr>
		</table>		

</div>

<? if (!isset($_REQUEST['refreshDialog'])) { ?> 
	</div>
<? } ?>
<?
print '<script type="text/javascript">
$(function() {
	ccm_activateFileManager(false, \'' . $searchInstance . '\');
});
</script>';
?>