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

<div id="ccm-file-manager-advanced" >
	
	<table id="ccm-file-manager-table" >
		<tr>
			<td class="ccm-search-form-advanced-col">
				<?php  Loader::element('files/search_form_advanced', array('fileSelector' => true)); ?>
			</td>		
			<?php  /* <div id="ccm-file-search-advanced-fields-gutter">&nbsp;</div> */ ?>		
			<td valign="top">	
				<div id="ccm-file-search-advanced-results-wrapper">
				
					<?php  Loader::element('files/upload_single', array('fileSelector' => true)); ?>
					
					<div id="ccm-file-search-results">
					
						<?php  Loader::element('files/search_results', array('fileSelector' => true, 'files' => $files, 'fileList' => $fileList, 'pagination' => $pagination)); ?>
					
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