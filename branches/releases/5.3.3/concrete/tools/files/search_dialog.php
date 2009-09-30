<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));

$cp = FilePermissions::getGlobal();
if (!$cp->canAccessFileManager()) {
	die(_("Unable to access the file manager."));
}

Loader::model('file_list');

$cnt = Loader::controller('/dashboard/files/search');
$fileList = $cnt->getRequestedSearchResults();
$files = $fileList->getPage();
$pagination = $fileList->getPagination();
?>

<div id="ccm-search-overlay" >
	
		<table id="ccm-search-form-table" >
			<tr>
				<td valign="top" class="ccm-search-form-advanced-col">
					<?php  Loader::element('files/search_form_advanced'); ?>
				</td>		
				<?php  /* <div id="ccm-file-search-advanced-fields-gutter">&nbsp;</div> */ ?>		
				<td valign="top">	
					
					<div id="ccm-search-advanced-results-wrapper">
					
						<?php  Loader::element('files/upload_single'); ?>
						
						<div id="ccm-search-results">
						
							<?php  Loader::element('files/search_results', array('files' => $files, 'fileList' => $fileList, 'pagination' => $pagination)); ?>
						
						</div>
					
					</div>
				
				</td>	
			</tr>
		</table>		

</div>

<?php 
print '<script type="text/javascript">
$(function() {
	ccm_activateFileManager();
});
</script>';
?>