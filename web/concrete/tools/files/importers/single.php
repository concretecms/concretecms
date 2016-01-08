<?php

defined('C5_EXECUTE') or die("Access Denied.");
use \Concrete\Core\File\EditResponse as FileEditResponse;

$u = new User();
$valt = Loader::helper('validation/token');
$cf = Loader::helper('file');

$fp = FilePermissions::getGlobal();
if (!$fp->canAddFiles()) {
	die(t("Unable to add files."));
}

$errorCode = -1;

$error = Loader::helper('validation/error');
if (isset($_POST['fID'])) {
	// we are replacing a file
	$fr = File::getByID($_REQUEST['fID']);
	$frp = new Permissions($fr);
	if (!$frp->canEditFileContents()) {
		$error->add(t('You do not have permission to modify this file.'));
	}
} else {
	$fr = false;
}

$r = new FileEditResponse();

if ($valt->validate('upload') && !$error->has()) {
	if (isset($_FILES['Filedata']) && (is_uploaded_file($_FILES['Filedata']['tmp_name']))) {
		if (!$fp->canAddFileType($cf->getExtension($_FILES['Filedata']['name']))) {
			$resp = FileImporter::E_FILE_INVALID_EXTENSION;
		} else {
			$fi = new FileImporter();
			$resp = $fi->import($_FILES['Filedata']['tmp_name'], $_FILES['Filedata']['name'], $fr);
			$r->setMessage(t('File uploaded successfully.'));
			if (is_object($fr)) {
				$r->setMessage(t('File replaced successfully.'));
			}

		}
		if (!($resp instanceof \Concrete\Core\File\Version)) {
			$errorCode = $resp;
		} else if (!is_object($fr)) {
			// we check $fr because we don't want to set it if we are replacing an existing file
			$respf = $resp->getFile();
			$respf->setOriginalPage($_POST['ocID']);
		} else {
			$respf = $fr;
		}
	} else {
		$errorCode = $_FILES['Filedata']['error'];
	}
} else if (isset($_FILES['Filedata'])) {
	// first, we check for validate upload token. If the posting of a file fails because of
	// post_max_size then this may not even be set, leading to misleading errors

	$error->add($valt->getErrorMessage());
} else {
	$errorCode = FileImporter::E_PHP_FILE_ERROR_DEFAULT;
}

if ($errorCode > -1) {
	$error->add(FileImporter::getErrorMessage($errorCode));
}

$r->setError($error);
if (is_object($respf)) {
	$r->setFile($respf);
}
$r->outputJSON();