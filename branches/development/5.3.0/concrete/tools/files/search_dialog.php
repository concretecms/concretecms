<?
defined('C5_EXECUTE') or die(_("Access Denied."));
$c = Page::getByPath("/dashboard/files");
$cp = new Permissions($c);
$u = new User();
if (!$cp->canRead()) {
	die(_("Unable to access the file manager."));
}



	
Loader::model('file_list');


Loader::element('files/search_form_simple');


$cnt = Loader::controller('/dashboard/files/search');
$fileList = $cnt->getRequestedSearchResults();

$files = $fileList->getPage();
$pagination = $fileList->getPagination();


print '<div id="ccm-file-search-results">';

Loader::element('files/search_results', array('fileSelector' => true, 'files' => $files, 'fileList' => $fileList, 'pagination' => $pagination));

print '</div>';