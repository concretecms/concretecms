<?php   defined('C5_EXECUTE') or die(_("Access Denied."));
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
<?php  if (!is_array($errors)) { ?>
	<p>
	<?php  if ($install) {
 		echo t('The package was successfully installed.');
	} else {
		echo t('The package was successfully downloaded and decompressed on your server.');
	} ?>
	</p>
<?php  } else { ?>
	<p><?php echo  t("The package could not be installed:") ?></p>
	<ol>
	<?php  foreach ($errors as $error) { ?>
		<li><?php echo  $error ?></li>
	<?php  } ?>
	</ol>
    <hr/>
	<p><?php echo  t("To install the package manually:") ?></p>
	<ol>
		<?php  if (!empty($ph)) { ?>
		<li>Download the the package from <a href="<?php echo $ph->get_remote_url($type, $cID);?>">here</a>.</li>
		<?php  } else { ?>
		<li>Download the the package.</li>
		<?php  } ?>
		<li>Upload and unpack the package on your web server.
		  Place the unpacked files in the 'packages' directory of the root of your Concrete5 installation.</li>
		<li>Goto the the <a href="<?php echo DIR_REL?>/index.php/dashboard/install">Add Functionality</a> page in your Concrete5 Dashboard.</li>
        <li>Click the 'Install' button next to the package name.</li>
	</ol>
<?php  } ?>
</div>
