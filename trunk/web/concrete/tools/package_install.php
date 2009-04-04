<?  defined('C5_EXECUTE') or die(_("Access Denied."));
$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : 'addon';
$cID = isset($_REQUEST['cID']) ? $_REQUEST['cID'] : null;
$install = isset($_REQUEST['install']) ? $_REQUEST['install'] : false;

if (!empty($cID)) {
	$ph = Loader::helper('package');
	$errors = $ph->install_remote($type, $cID, $install);
	if (is_array($errors)) {
		$errors = Package::mapError($errors);
	}
} else {
	$errors = array(t('No package specified.'));
}

?>

<div>
<? if (!is_array($errors)) { ?>
	<p>
	<? if ($install) {
 		echo t('The package was successfully installed.');
	} else {
		echo t('The package was successfully downloaded and decompressed on your server.');
	} ?>
	</p>
<? } else { ?>
	<p><?= t("The package could not be installed:") ?></p>
	<ol>
	<? foreach ($errors as $error) { ?>
		<li><?= $error ?></li>
	<? } ?>
	</ol>
<? } ?>
</div>
