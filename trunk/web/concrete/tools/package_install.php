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
    <hr/>
	<p><?= t("To install the package manually:") ?></p>
	<ol>
		<? if (!empty($ph)) { ?>
		<li>Download the the package from <a href="<?=$ph->get_remote_url($type, $cID);?>">here</a>.</li>
		<? } else { ?>
		<li>Download the the package.</li>
		<? } ?>
		<li>Upload and unpack the package on your web server.
		  Place the unpacked files in the 'packages' directory of the root of your Concrete5 installation.</li>
		<li>Goto the the <a href="<?=DIR_REL?>/index.php/dashboard/install">Add Functionality</a> page in your Concrete5 Dashboard.</li>
        <li>Click the 'Install' button next to the package name.</li>
	</ol>
<? } ?>
</div>
