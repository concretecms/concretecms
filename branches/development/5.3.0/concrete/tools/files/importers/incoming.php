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


if ($valt->validate('import_incoming')) {
	if( !empty($_POST) ) {
		$fi = new FileImporter();
		foreach($_POST as $k=>$name) {
			if(preg_match("#^send_file#", $k)) {
				$resp = $fi->import(DIR_FILES_INCOMING .'/'. $name, $name);
				if (!($resp instanceof FileVersion)) {
					switch($resp) {
						case FileImporter::E_FILE_INVALID_EXTENSION:
							$error .= t('Invalid file extension.');
							break;
						case FileImporter::E_FILE_INVALID:
							$error .= t('Invalid file.');
							break;
						
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
	<? } ?>
</script>
</head>
<body>
</body>
</html>