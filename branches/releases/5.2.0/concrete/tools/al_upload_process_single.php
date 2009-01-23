<?php 

defined('C5_EXECUTE') or die(_("Access Denied."));
$c = Page::getByPath("/dashboard/mediabrowser");
$cp = new Permissions($c);
$u = new User();
if (!$cp->canRead()) {
	die(_("Access Denied."));
}
$valt = Loader::helper('validation/token');

require_once(DIR_FILES_BLOCK_TYPES_CORE . '/library_file/controller.php');
$error = "";
if ($valt->validate('upload')) {
	if (isset($_FILES['Filedata'])) {
		
		if(is_uploaded_file($_FILES['Filedata']['tmp_name'])) {
			$fh = Loader::helper('file');
			if($fh->hasAllowedExtension($_FILES['Filedata']['name'])) {	
				$bt = BlockType::getByHandle('library_file');
				$data = array();
				$data['file'] = $_FILES['Filedata']['tmp_name'];
				$data['name'] = $_FILES['Filedata']['name'];
				$nb = $bt->add($data);
			} else {
				$error = t('Invalid file extension.');
			}
		} else {
			$error = t('An error occured while uploading your file');
		}
	} else {
		$error = t('An error occured while uploading your file');
	}
} else {
	$error = $valt->getErrorMessage();
}
?>
<html>
<head>
<script language="javascript">
	window.parent.ccm_alRefresh();
	<?php  if(strlen($error)) { ?>
		alert('<?php echo $error?>');
	<?php  } ?>
</script>
</head>
<body>
</body>
</html>