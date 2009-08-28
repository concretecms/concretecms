<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
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


Loader::element('files/search_results', array('files' => $files, 'fileList' => $fileList, 'pagination' => $pagination));
