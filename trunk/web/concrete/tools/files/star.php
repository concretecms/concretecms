<?php
	defined('C5_EXECUTE') or die(_("Access Denied."));
	Loader::model('file_set');
	$file_set = FileSet::createAndGetSet('Starred Files',FileSet::TYPE_STARRED);	
	switch ($_POST['action']) {
		case 'star':			
			$file_set->AddFileToSet($_POST['file-id']);
			break;
		case 'unstar':
			$file_set->RemoveFileFromSet($_POST['file-id']);
			break;
		default:
			throw new Exception('INVALID ACTION');
	}
