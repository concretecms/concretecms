<?
defined('C5_EXECUTE') or die("Access Denied.");
$pla = \Concrete\Core\Package\PackageList::get();
$pl = $pla->getPackages();

foreach($pl as $p) {
	if ($p->isPackageInstalled()) {
		$pkg = Loader::package($p->getPackageHandle());
		if (is_object($pkg)) {
			// handle updates
			if (ENABLE_AUTO_UPDATE_PACKAGES) {
				$pkgInstalledVersion = $p->getPackageVersion();
				$pkgFileVersion = $pkg->getPackageVersion();
				if (version_compare($pkgFileVersion, $pkgInstalledVersion, '>')) {
					$currentLocale = Localization::activeLocale();
					if ($currentLocale != 'en_US') {
						Localization::changeLocale('en_US');
					}
					$p->upgradeCoreData();
					$p->upgrade();
					if ($currentLocale != 'en_US') {
						Localization::changeLocale($currentLocale);
					}
				}
			}
			$pkg->setupPackageLocalization();
			if (method_exists($pkg, 'on_start')) {
				$pkg->on_start();
			}
		}
	}
}