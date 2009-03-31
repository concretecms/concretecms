<?
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
			<td>
				<? Loader::element('files/search_form_advanced'); ?>
			</td>		
			<? /* <div id="ccm-file-search-advanced-fields-gutter">&nbsp;</div> */ ?>		
			<td>	
				<div id="ccm-file-search-advanced-results-wrapper">
				
					<? Loader::element('files/upload_single'); ?>
					
					<div id="ccm-file-search-results">
					
						<? Loader::element('files/search_results', array('fileSelector' => true, 'files' => $files, 'fileList' => $fileList, 'pagination' => $pagination)); ?>
					
					</div>
				
				</div> 	
			</td>	
		</tr>
	</table>

</div>

<?
print '<script type="text/javascript">
$(function() {
	ccm_activateFileManager();
});
</script>';
?>