<?php
defined('C5_EXECUTE') or die(_("Access Denied."));
$c = Page::getByPath("/dashboard/mediabrowser");
$cp = new Permissions($c);
$u = new User();
if (!$cp->canRead()) {
	die(_("Access Denied."));
}


$valt = Loader::helper('validation/token');
Loader::library("file/importer");

$error = "";

if ($valt->validate('upload')) {
	if (isset($_FILES['Filedata']) && (is_uploaded_file($_FILES['Filedata']['tmp_name']))) {
		$fi = new FileImporter();
		$resp = $fi->import($_FILES['Filedata']['tmp_name'], $_FILES['Filedata']['name']);
		if (!($resp instanceof FileVersion)) {
			switch($resp) {
				case FileImporter::E_FILE_INVALID_EXTENSION:
					$error = t('Invalid file extension.');
					break;
				case FileImporter::E_FILE_INVALID:
					$error = t('Invalid file.');
					break;
				
			}
		}
	}
} else {
	$error = $valt->getErrorMessage();
}

if (strlen($error) > 0) {
	echo $error;
}
else{
	echo 'Complete';
}