<?

defined('C5_EXECUTE') or die("Access Denied.");
$u = new User();
$valt = Loader::helper('validation/token');
Loader::library("file/importer");
$cf = Loader::helper('file');

$fp = FilePermissions::getGlobal();
if (!$fp->canAddFiles()) {
	die(t("Unable to add files."));
}

$error = "";
$errorCode = -1;

if (isset($_POST['fID'])) {
	// we are replacing a file
	$fr = File::getByID($_REQUEST['fID']);
} else {
	$fr = false;
}

$searchInstance = $_POST['searchInstance'];

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
		} else if (!is_object($fr)) {
			// we check $fr because we don't want to set it if we are replacing an existing file
			$respf = $resp->getFile();
			$respf->setOriginalPage($_POST['ocID']);
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
?>
<html>
<head>
<script language="javascript">
	<? if(strlen($error)) { ?>
		window.parent.ccmAlert.notice("<?=t('Upload Error')?>", "<?=str_replace("\n", '', nl2br($error))?>");
		window.parent.ccm_alResetSingle();
	<? } else { ?>
		highlight = new Array();
		highlight.push(<?=$resp->getFileID()?>);
		
		<? if (is_object($fr)) { ?>
			window.parent.jQuery.fn.dialog.closeTop();
		<? } ?>
		
		window.parent.ccm_uploadedFiles.push(<?=intval($resp->getFileID())?>);
		setTimeout(function() { 
			window.parent.ccm_filesUploadedDialog('<?=$searchInstance?>');
			window.parent.ccm_alResetSingle();
		}, 100);
	<? } ?>
</script>
</head>
<body>
</body>
</html>