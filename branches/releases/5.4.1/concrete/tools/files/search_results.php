<?php 
defined('C5_EXECUTE') or die("Access Denied.");
$fp = FilePermissions::getGlobal();
if (!$fp->canAccessFileManager()) {
	die(_("Unable to access the file manager."));
}

$u = new User();
	
Loader::model('file_list');

$cnt = Loader::controller('/dashboard/files/search');
$fileList = $cnt->getRequestedSearchResults();

$files = $fileList->getPage();
$pagination = $fileList->getPagination();
$searchRequest = $cnt->get('searchRequest');
$columns = $cnt->get('columns');

Loader::element('files/search_results', array('files' => $files, 'columns' => $columns, 'searchRequest' => $searchRequest,  'fileList' => $fileList, 'pagination' => $pagination));
