<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
$jsh = Loader::helper("json");
$cf = Loader::helper("file");
$valt = Loader::helper('validation/token');
Loader::library("file/importer");
$fp = FilePermissions::getGlobal();
if (!$fp->canAddFiles()) {
	$error = FileImporter::getErrorMessage(FileImporter::E_PHP_FILE_ERROR_DEFAULT);
	$info = array('message'=>$error, 'error' => true);
	print $jsh->encode($info);
	exit;
}
$u = new User();


$error = "";
$errorCode = -1;

if ($valt->validate('upload')) {
	if (isset($_FILES['Filedata']) && (is_uploaded_file($_FILES['Filedata']['tmp_name']))) {
		if (!$fp->canAddFileType($cf->getExtension($_FILES['Filedata']['name']))) {
			$resp = FileImporter::E_FILE_INVALID_EXTENSION;
		} else {
			$fi = new FileImporter();
			$resp = $fi->import($_FILES['Filedata']['tmp_name'], $_FILES['Filedata']['name'], $fr);
		}
		if (!($resp instanceof FileVersion)) {
			$errorCode = $resp;
		}
	} else {
		$errorCode = $_FILES['Filedata']['error'];
	}
} else if (isset($_FILES['Filedata'])) {
	// first, we check for validate upload token. If the posting of a file fails because of
	// post_max_size then this may not even be set, leading to misleading errors

	$error = $valt->getErrorMessage();
} else {
	$errorCode = FileImporter::E_PHP_FILE_ERROR_DEFAULT;
}

if ($errorCode > -1 && $error == '') {
	$error = FileImporter::getErrorMessage($errorCode);
}

if (strlen($error) > 0) {
	$info = array('message'=>$error, 'error' => true);
} else {
	$id = $resp->getFileID();			
	$info['message'] = t('Upload Complete.');
	$info['error'] = false;
	$info['id']		 = $resp->getFileID();
}

print $jsh->encode($info);
exit;