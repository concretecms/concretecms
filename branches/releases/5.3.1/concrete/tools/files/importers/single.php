<?php 

defined('C5_EXECUTE') or die(_("Access Denied."));
$u = new User();
$valt = Loader::helper('validation/token');
Loader::library("file/importer");
$cf = Loader::helper('file');

$fp = FilePermissions::getGlobal();
if (!$fp->canAddFiles()) {
	die(_("Unable to add files."));
}

$error = "";

if (isset($_POST['fID'])) {
	// we are replacing a file
	$fr = File::getByID($_REQUEST['fID']);
} else {
	$fr = false;
}

if ($valt->validate('upload')) {
	if (isset($_FILES['Filedata']) && (is_uploaded_file($_FILES['Filedata']['tmp_name']))) {
		if (!$fp->canAddFileType($cf->getExtension($_FILES['Filedata']['name']))) {
			$resp = FileImporter::E_FILE_INVALID_EXTENSION;
		} else {
			$fi = new FileImporter();
			$resp = $fi->import($_FILES['Filedata']['tmp_name'], $_FILES['Filedata']['name'], $fr);
		}
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
?>
<html>
<head>
<script language="javascript">
	<?php  if(strlen($error)) { ?>
		alert('<?php echo $error?>');
		window.parent.ccm_alResetSingle();
	<?php  } else { ?>
		highlight = new Array();
		highlight.push(<?php echo $resp->getFileID()?>);
		window.parent.ccm_alRefresh(highlight);
		
		<?php  if (is_object($fr)) { ?>
			window.parent.jQuery.fn.dialog.closeTop();
		<?php  } ?>
	<?php  } ?>
</script>
</head>
<body>
</body>
</html>