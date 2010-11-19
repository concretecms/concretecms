<?php 
defined('C5_EXECUTE') or die("Access Denied.");

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

$alType = 'false';
if (isset($_REQUEST['disable_choose']) && $_REQUEST['disable_choose'] == 1) { 
	$alType = 'BROWSE';
}
?>

<?php  if (!isset($_REQUEST['refreshDialog'])) { ?> 
	<div id="ccm-<?php echo $searchInstance?>-overlay-wrapper">
<?php  } ?>
<div id="ccm-<?php echo $searchInstance?>-search-overlay">
	<input type="hidden" name="dialogAction" value="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/files/search_dialog?ocID=<?php echo $_REQUEST['ocID']?>&searchInstance=<?php echo $searchInstance?>&disable_choose=<?php echo $_REQUEST['disable_choose']?>" />
		
		<table id="ccm-search-form-table" >
			<tr>
				<td valign="top" class="ccm-search-form-advanced-col">
					<?php  Loader::element('files/search_form_advanced', array('searchInstance' => $searchInstance, 'columns' => $columns, 'searchRequest' => $searchRequest)); ?>
				</td>		
				<?php  /* <div id="ccm-<?php echo $searchInstance?>-search-advanced-fields-gutter">&nbsp;</div> */ ?>		
				<td valign="top" width="100%">	
					
					<div id="ccm-search-advanced-results-wrapper">
					
						<?php  Loader::element('files/upload_single', array('searchInstance' => $searchInstance, 'ocID' => $ocID)); ?>
						
						<div id="ccm-<?php echo $searchInstance?>-search-results" class="ccm-file-list">
						
							<?php  Loader::element('files/search_results', array('searchInstance' => $searchInstance, 'columns' => $columns, 'searchRequest' => $searchRequest, 'files' => $files, 'fileList' => $fileList, 'pagination' => $pagination)); ?>
						
						</div>
					
					</div>
				
				</td>	
			</tr>
		</table>		

</div>

<?php  if (!isset($_REQUEST['refreshDialog'])) { ?> 
	</div>
<?php  } ?>
<?php 
print '<script type="text/javascript">
$(function() {
	ccm_activateFileManager(\'' . $alType . '\', \'' . $searchInstance . '\');
});
</script>';
?>