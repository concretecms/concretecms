<?
defined('C5_EXECUTE') or die(_("Access Denied."));
$c = Page::getByPath("/dashboard/files");
$cp = new Permissions($c);
$u = new User();
if (!$cp->canRead()) {
	die(_("Unable to access the file manager."));
}



	
Loader::model('file_list');

$cnt = Loader::controller('/dashboard/files/search');
$fileList = $cnt->getRequestedSearchResults();
$files = $fileList->getPage();
$pagination = $fileList->getPagination();

Loader::element('files/search_form_advanced'); ?>


<div id="ccm-file-search-advanced-fields-gutter">&nbsp;</div>

<div id="ccm-file-search-advanced-results-wrapper">

<? Loader::element('files/upload_single'); ?>

<div id="ccm-file-search-results">

<? Loader::element('files/search_results', array('fileSelector' => true, 'files' => $files, 'fileList' => $fileList, 'pagination' => $pagination)); ?>

</div>

</div>

</div>

<?
print '<script type="text/javascript">
$(function() {
	ccm_activateFileManager();
});
</script>';