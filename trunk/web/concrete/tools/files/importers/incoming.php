<?

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
				$resp = $fi->import(DIR_FILES_INCOMING .'/'. $name, $name, $fr);
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
	<? if(strlen($error)) { ?>
		alert('<?=$error?>');
		window.parent.ccm_alResetSingle();
	<? } else { ?>
		window.parent.ccm_alRefresh();
		//window.parent.jQuery.fn.dialog.closeTop();
		highlight = new Array();
		<? foreach($files as $resp) { ?>
			highlight.push(<?=$resp->getFileID()?>);
		<? } ?>
		window.parent.ccm_alRefresh(highlight);
	<? } ?>
</script>
</head>
<body>
</body>
</html>