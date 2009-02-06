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

Loader::element('files/search_results', array('files' => $files, 'fileList' => $fileList, 'pagination' => $pagination));
