<?php 

defined('C5_EXECUTE') or die(_("Access Denied."));
$u = new User();
$fp = FilePermissions::getGlobal();
if (!$fp->canAddFiles()) {
	die(_("Unable to add files."));
}
$cf = Loader::helper("file");
$valt = Loader::helper('validation/token');
Loader::library("file/importer");

$error = "";

if (isset($_POST['fID'])) {
	// we are replacing a file
	$fr = File::getByID($_REQUEST['fID']);
} else {
	$fr = false;
}

$files = array();
if ($valt->validate('import_incoming')) {
	if( !empty($_POST) ) {
		$fi = new FileImporter();
		foreach($_POST as $k=>$name) {
			if(preg_match("#^send_file#", $k)) {
				if (!$fp->canAddFileType($cf->getExtension($name))) {
					$resp = FileImporter::E_FILE_INVALID_EXTENSION;
				} else {
					$resp = $fi->import(DIR_FILES_INCOMING .'/'. $name, $name, $fr);
				}
				if (!($resp instanceof FileVersion)) {
					switch($resp) {
						case FileImporter::E_FILE_INVALID_EXTENSION:
							$error .= t('Invalid file extension.');
							break;
						case FileImporter::E_FILE_INVALID:
							$error .= t('Invalid file.');
							break;
						
					}
				} else {
					$files[] = $resp;
					if ($_POST['removeFilesAfterPost'] == 1) {
						unlink(DIR_FILES_INCOMING .'/'. $name);
					}
				}
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
		<?php  foreach($files as $resp) { ?>
			highlight.push(<?php echo $resp->getFileID()?>);
		<?php  } ?>
		window.parent.jQuery.fn.dialog.closeTop();
		window.parent.ccm_alRefresh(highlight);
	<?php  } ?>
</script>
</head>
<body>
</body>
</html>