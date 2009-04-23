<?php   defined('C5_EXECUTE') or die(_("Access Denied."));
$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : 'addon';
$cID = isset($_REQUEST['cID']) ? $_REQUEST['cID'] : null;
$install = isset($_REQUEST['install']) ? $_REQUEST['install'] : false;

if (!empty($cID)) {
	$ph = Loader::helper('package');
	$error = $ph->install_remote($type, $cID, $install);
} else {
	$error = t('No package specified.');
}

if (empty($error)) {
	if ($install) {
		$msg = t('The package was successfully installed.');
	} else {
		$msg = t('The package was successfully downloaded and decompressed on your server.');
	}
} else {
	$msg = t("The package could not be installed: %s.", $error);
}
?>

<div><p><?php echo $msg?></p></div>