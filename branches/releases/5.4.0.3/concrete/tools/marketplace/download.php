<?php   defined('C5_EXECUTE') or die(_("Access Denied."));

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
	<?php  if ($install) {
 		echo t('The package was successfully installed.');
	} else {
		echo t('The package was successfully downloaded and decompressed on your server.');
	} 
	print '<br><br>';
	print Loader::helper('concrete/interface')->button_js(t('Return'), 'javascript:ccm_getMarketplaceItem.onComplete()')?>
	
	</p>
<?php  } else { ?>
	<p><?php echo  t("The package could not be installed:") ?></p>

	<?php  $error->output(); ?>

    <hr/>
    <?php  if (is_object($mri)) { ?>
	<p><?php echo  t("To install the package manually:") ?></p>
	<ol>
		<li><?php echo t('Download the the package from <a href="%s">here</a>.', $mri->getRemoteURL())?></li>
		<li><?php echo t('Upload and unpack the package on your web server. Place the unpacked files in the packages directory of the root of your concrete5 installation.')?></li>
		<li><?php echo t('Go to the the <a href="%s">Add Functionality</a> page in your concrete5 Dashboard.', View::url('/dashboard/install'))?></li>
        <li><?php echo t('Click the Install button next to the package name.')?></li>
	</ol>
	<?php  } ?>
<?php  } ?>