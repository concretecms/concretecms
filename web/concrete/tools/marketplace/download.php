<?  defined('C5_EXECUTE') or die("Access Denied.");

$tp = new TaskPermission();
if (!$tp->canInstallPackages()) { ?>
	<p><?=t('You do not have permission to download packages from the marketplace.')?></p>
	<? exit;

}

Loader::library('marketplace');
Loader::model('marketplace_remote_item');

$mpID = $_REQUEST['mpID'];
$install = isset($_REQUEST['install']) ? $_REQUEST['install'] : false;
$error = Loader::helper('validation/error');

if (!empty($mpID)) {
	
	$mri = MarketplaceRemoteItem::getByID($mpID);
	if (is_object($mri)) { 
		$r = $mri->download();
		if ($r != false) {
			if (is_array($r)) {
				$errors = Package::mapError($r);
				foreach($errors as $e) {
					$error->add($e);
				}
			} else {
				$error->add($r);
			}
		}
	}
}

if (!is_object($mri)) {
	$error->add(t('Invalid package or no package specified.'));
}

if (!$error->has() && $install) {
   	$tests = Package::testForInstall($mri->getHandle());
   	if (is_array($tests)) {
   		$results = Package::mapError($tests);
   		foreach($results as $te) {
   			$error->add($te);
   		}
   	} else {
		$p = Loader::package($mri->getHandle());
		try {
			$p->install();
		} catch(Exception $e) {
			$error->add($e->getMessage());
		}
	}
}

if (!$error->has()) { ?>
	<p>
	<? if ($install) {
		$_pkg = Package::getByHandle($p->getPackageHandle());
		if ($_pkg->hasInstallPostScreen()) { 
			Loader::element('dashboard/install_post', false, $_pkg->getPackageHandle());
		} else {
	 		echo t('The package was successfully installed.');
	 	}
	} else {
		echo t('The package was successfully downloaded and decompressed on your server.');
	} 
	print '<div class="dialog-buttons">';
	print Loader::helper('concrete/interface')->button_js(t('Return'), 'javascript:ccm_getMarketplaceItem.onComplete()', 'right');
	print '</div>';
	?>
	</p>
<? } else { ?>
	<p><?= t("The package could not be installed:") ?></p>

	<? $error->output(); ?>

    <hr/>
    <? if (is_object($mri)) { ?>
	<p><?= t("To install the package manually:") ?></p>
	<ol>
		<li><?=t('Download the package from <a href="%s">here</a>.', $mri->getRemoteURL())?></li>
		<li><?=t('Upload and unpack the package on your web server. Place the unpacked files in the packages directory of the root of your concrete5 installation.')?></li>
		<li><?=t('Go to the <a href="%s">Add Functionality</a> page in your concrete5 Dashboard.', View::url('/dashboard/install'))?></li>
        <li><?=t('Click the Install button next to the package name.')?></li>
	</ol>
	<div class="dialog-buttons"></div>
	<? } ?>
<? } ?>