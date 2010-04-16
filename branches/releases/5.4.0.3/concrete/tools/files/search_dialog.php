<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));

$cp = FilePermissions::getGlobal();
if (!$cp->canAccessFileManager()) {
	die(_("Unable to access the file manager."));
}

Loader::model('file_list');

$searchInstance = $page . time();

$cnt = Loader::controller('/dashboard/files/search');
$fileList = $cnt->getRequestedSearchResults();
$files = $fileList->getPage();
$pagination = $fileList->getPagination();
$searchRequest = $fileList->getSearchRequest();
?>

<div id="ccm-search-overlay" >
	
		<table id="ccm-search-form-table" >
			<tr>
				<td valign="top" class="ccm-search-form-advanced-col">
					<?php  Loader::element('files/search_form_advanced', array('searchInstance' => $searchInstance, 'searchRequest' => $searchRequest)); ?>
				</td>		
				<?php  /* <div id="ccm-<?php echo $searchInstance?>-search-advanced-fields-gutter">&nbsp;</div> */ ?>		
				<td valign="top" width="100%">	
					
					<div id="ccm-search-advanced-results-wrapper">
					
						<?php  Loader::element('files/upload_single', array('searchInstance' => $searchInstance)); ?>
						
						<div id="ccm-<?php echo $searchInstance?>-search-results" class="ccm-file-list">
						
							<?php  Loader::element('files/search_results', array('searchInstance' => $searchInstance, 'files' => $files, 'fileList' => $fileList, 'pagination' => $pagination)); ?>
						
						</div>
					
					</div>
				
				</td>	
			</tr>
		</table>		

</div>

<?php 
print '<script type="text/javascript">
$(function() {
	ccm_activateFileManager(false, \'' . $searchInstance . '\');
});
</script>';
?>