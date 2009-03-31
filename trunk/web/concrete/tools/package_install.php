<?  defined('C5_EXECUTE') or die(_("Access Denied."));
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
	$msg = "<p>The package was successfully " . 
	       ($install ? "installed." : "downloaded and unzipped onto your server.</p>");
} else {
	$msg = "<p>The package could not be installed.</p>" .
	       "<p>$error</p>";
}
?>

<div><?=$msg?></div>
