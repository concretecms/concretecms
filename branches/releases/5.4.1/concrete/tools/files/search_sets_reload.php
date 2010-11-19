<?php 
defined('C5_EXECUTE') or die("Access Denied.");

$cp = FilePermissions::getGlobal();
if (!$cp->canAccessFileManager()) {
	die(_("Unable to access the file manager."));
}

Loader::model('file_list');
Loader::model('file_set');

$fileList = new FileList();
$fileList->enableStickySearchRequest();
$req = $fileList->getSearchRequest();

Loader::element('files/search_form_sets', array('searchInstance' => $_REQUEST['searchInstance'], 'searchRequest' => $req));